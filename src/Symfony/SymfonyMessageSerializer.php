<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\Symfony;

use ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed;
use ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed;
use ServiceBus\MessageSerializer\Exceptions\EncodeMessageFailed;
use ServiceBus\MessageSerializer\Exceptions\NormalizationFailed;
use ServiceBus\MessageSerializer\MessageDecoder;
use ServiceBus\MessageSerializer\MessageEncoder;
use ServiceBus\MessageSerializer\Symfony\Extensions\EmptyDataNormalizer;
use ServiceBus\MessageSerializer\Symfony\Extensions\PropertyNameConverter;
use ServiceBus\MessageSerializer\Symfony\Extensions\PropertyNormalizerWrapper;
use ServiceBus\MessageSerializer\Symfony\Extractor\CombinedExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use function ServiceBus\Common\jsonDecode;
use function ServiceBus\Common\jsonEncode;

/**
 *
 */
final class SymfonyMessageSerializer implements MessageEncoder, MessageDecoder
{
    /**
     * Symfony normalizer\denormalizer.
     *
     * @var SymfonySerializer\Serializer
     */
    private $normalizer;

    /**
     * @param SymfonySerializer\Normalizer\DenormalizerInterface[]|SymfonySerializer\Normalizer\NormalizerInterface[] $normalizers
     */
    public function __construct(array $normalizers = [])
    {
        $extractor = \PHP_VERSION_ID >= 70400 ? new CombinedExtractor() : new PhpDocExtractor();

        $defaultNormalizers = [
            new DateTimeNormalizer(['datetime_format' => 'c']),
            new SymfonySerializer\Normalizer\ArrayDenormalizer(),
            new PropertyNormalizerWrapper(null, new PropertyNameConverter(), $extractor),
            new EmptyDataNormalizer(),
        ];

        /** @psalm-var array<array-key, (\Symfony\Component\Serializer\Normalizer\NormalizerInterface|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface)> $normalizers */
        $normalizers = \array_merge($normalizers, $defaultNormalizers);

        $this->normalizer = new SymfonySerializer\Serializer($normalizers);
    }

    /**
     * {@inheritdoc}
     */
    public function encode(object $message): string
    {
        try
        {
            $data = ['message' => $this->normalize($message), 'namespace' => \get_class($message)];

            return jsonEncode($data);
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

    /**
     * {@inheritdoc}
     */
    public function decode(string $serializedMessage): object
    {
        try
        {
            $data = jsonDecode($serializedMessage);

            self::validateUnserializedData($data);

            /** @var object $object */
            $object = $this->denormalize($data['message'], $data['namespace']);

            return $object;
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
     * {@inheritdoc}
     */
    public function denormalize(array $payload, string $class): object
    {
        try
        {
            /** @var object $object */
            $object = $this->normalizer->denormalize(
                $payload,
                $class
            );

            return $object;
        }
        catch (\Throwable $throwable)
        {
            throw new DenormalizeFailed($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(object $message): array
    {
        try
        {
            $data = $this->normalizer->normalize($message);

            if (\is_array($data) === true)
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

    /**
     * @psalm-assert array{message:array<string, string|int|float|array|null>, namespace:class-string} $data
     *
     * @throws \UnexpectedValueException
     */
    private static function validateUnserializedData(array $data): void
    {
        /** Let's check if there are mandatory fields */
        if (
            isset($data['namespace']) === false ||
            isset($data['message']) === false
        ) {
            throw new \UnexpectedValueException(
                'The serialized data must contains a "namespace" field (indicates the message class) and "message" (indicates the message parameters)'
            );
        }

        if (\is_array($data['message']) === false)
        {
            throw new \UnexpectedValueException('"message" field from serialized data should be an array');
        }

        if (\is_string($data['namespace']) === false)
        {
            throw new \UnexpectedValueException('"namespace" field from serialized data should be a string');
        }

        /**
         * Let's check if the specified class exists.
         *
         * @psalm-suppress DocblockTypeContradiction
         */
        if ($data['namespace'] === '' || \class_exists($data['namespace']) === false)
        {
            throw new \UnexpectedValueException(
                \sprintf('Class "%s" not found', $data['namespace'])
            );
        }
    }
}
