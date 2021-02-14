<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 0);

namespace ServiceBus\MessageSerializer\Symfony;

use ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed;
use ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed;
use ServiceBus\MessageSerializer\Exceptions\EncodeMessageFailed;
use ServiceBus\MessageSerializer\Exceptions\NormalizationFailed;
use ServiceBus\MessageSerializer\MessageSerializer;
use ServiceBus\MessageSerializer\Symfony\Extensions\EmptyDataNormalizer;
use ServiceBus\MessageSerializer\Symfony\Extensions\PropertyNameConverter;
use ServiceBus\MessageSerializer\Symfony\Extensions\PropertyNormalizerWrapper;
use ServiceBus\MessageSerializer\Symfony\Extractor\CombinedExtractor;
use Symfony\Component\Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use function ServiceBus\Common\jsonDecode;
use function ServiceBus\Common\jsonEncode;

/**
 *
 */
final class SymfonySerializer implements MessageSerializer
{
    /**
     * Symfony normalizer\denormalizer.
     *
     * @var Serializer\Serializer
     */
    private $normalizer;

    /**
     * @param Serializer\Normalizer\DenormalizerInterface[]|Serializer\Normalizer\NormalizerInterface[] $normalizers
     */
    public function __construct(array $normalizers = [])
    {
        $extractor = new CombinedExtractor();

        $defaultNormalizers = [
            new DateTimeNormalizer(['datetime_format' => 'c']),
            new Serializer\Normalizer\ArrayDenormalizer(),
            new PropertyNormalizerWrapper(null, new PropertyNameConverter(), $extractor),
            new EmptyDataNormalizer(),
        ];

        /** @psalm-var array<array-key, (\Symfony\Component\Serializer\Normalizer\NormalizerInterface|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface)> $normalizers */
        $normalizers = \array_merge($normalizers, $defaultNormalizers);

        $this->normalizer = new Serializer\Serializer($normalizers);
    }

    public function encode(object $message): string
    {
        try
        {
            return jsonEncode(
                $this->normalize($message)
            );
        }
        catch (\Throwable $throwable)
        {
            throw new EncodeMessageFailed(
                \sprintf('Message serialization failed: %s', $throwable->getMessage()),
                (int) $throwable->getCode(),
                $throwable
            );
        }
    }

    public function decode(string $serializedMessage, string $messageClass): object
    {
        try
        {
            return $this->denormalize(
                payload: jsonDecode($serializedMessage),
                messageClass: $messageClass
            );
        }
        catch (\Throwable $throwable)
        {
            throw new DecodeMessageFailed(
                \sprintf('Message deserialization failed: %s', $throwable->getMessage()),
                (int) $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * @template T
     * @psalm-param class-string<T> $messageClass
     * @psalm-return T
     */
    public function denormalize(array $payload, string $messageClass): object
    {
        try
        {
            /** @var T $object */
            $object = $this->normalizer->denormalize(
                $payload,
                $messageClass
            );

            return $object;
        }
        catch (\Throwable $throwable)
        {
            throw new DenormalizeFailed($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }

    public function normalize(object $message): array
    {
        try
        {
            $data = $this->normalizer->normalize($message);

            if (\is_array($data))
            {
                /** @psalm-var array<string, mixed> $data */

                return $data;
            }

            // @codeCoverageIgnoreStart
            throw new \UnexpectedValueException(
                \sprintf(
                    'The normalization was to return the array. Type "%s" was obtained when object "%s" was normalized',
                    \gettype($data),
                    \get_class($message)
                )
            );
            // @codeCoverageIgnoreEnd
        }
        catch (\Throwable $throwable)
        {
            throw new NormalizationFailed($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }

    public function contentType(): string
    {
        return 'application/json';
    }
}
