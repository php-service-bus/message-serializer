<?php

/**
 * PHP Service Bus serializer component
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\Symfony;

use ServiceBus\Common\Messages\Message;
use ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed;
use ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed;
use ServiceBus\MessageSerializer\Exceptions\EncodeMessageFailed;
use ServiceBus\MessageSerializer\Exceptions\NormalizationFailed;
use ServiceBus\MessageSerializer\JsonSerializer;
use ServiceBus\MessageSerializer\MessageDecoder;
use ServiceBus\MessageSerializer\MessageEncoder;
use ServiceBus\MessageSerializer\Serializer;
use ServiceBus\MessageSerializer\SymfonyNormalizer\Extensions\EmptyDataDenormalizer;
use ServiceBus\MessageSerializer\SymfonyNormalizer\Extensions\EmptyDataNormalizer;
use ServiceBus\MessageSerializer\SymfonyNormalizer\Extensions\PropertyNameConverter;
use ServiceBus\MessageSerializer\SymfonyNormalizer\Extensions\PropertyNormalizerWrapper;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer as SymfonySerializer;

/**
 *
 */
final class SymfonyMessageSerializer implements MessageEncoder, MessageDecoder
{
    /**
     * Symfony normalizer\denormalizer
     *
     * @var SymfonySerializer\Serializer
     */
    private $normalizer;

    /**
     * Serializer implementation
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpDocSignatureInspection
     *
     * @param Serializer                                                                                              $serializer
     * @param SymfonySerializer\Normalizer\NormalizerInterface[]|SymfonySerializer\Normalizer\DenormalizerInterface[] $normalizers
     */
    public function __construct(Serializer $serializer = null, array $normalizers = [])
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $defaultNormalizers = [
            new SymfonySerializer\Normalizer\DateTimeNormalizer(['datetime_format' => 'c']),
            new SymfonySerializer\Normalizer\ArrayDenormalizer(),
            new PropertyNormalizerWrapper(null, new PropertyNameConverter(), new PhpDocExtractor()),
            new EmptyDataDenormalizer(),
            new EmptyDataNormalizer()
        ];

        /** @var array<array-key, (\Symfony\Component\Serializer\Normalizer\NormalizerInterface|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface)> $normalizers */
        $normalizers = \array_merge($defaultNormalizers, $normalizers);

        $this->normalizer = new SymfonySerializer\Serializer($normalizers);
        $this->serializer = $serializer ?? new JsonSerializer();
    }

    /**
     * @inheritDoc
     */
    public function encode(Message $message): string
    {
        try
        {
            $data = ['message' => $this->normalize($message), 'namespace' => \get_class($message)];

            return $this->serializer->serialize($data);
        }
        catch(\Throwable $throwable)
        {
            throw new EncodeMessageFailed($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }

    /**
     * @inheritDoc
     */
    public function decode(string $serializedMessage): Message
    {
        try
        {
            /** @var array{message:array<string, string|int|float|null>, namespace:class-string} $data */
            $data = $this->serializer->unserialize($serializedMessage);

            self::validateUnserializedData($data);

            /** @var Message $object */
            $object = $this->denormalize($data['message'], $data['namespace']);

            return $object;
        }
        catch(\Throwable $throwable)
        {
            throw new DecodeMessageFailed($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }

    /**
     * @inheritDoc
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
        catch(\Throwable $throwable)
        {
            throw new DenormalizeFailed($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }

    /**
     * @inheritDoc
     */
    public function normalize(object $message): array
    {
        try
        {
            $data = $this->normalizer->normalize($message);

            if(true === \is_array($data))
            {
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
        catch(\Throwable $throwable)
        {
            throw new NormalizationFailed($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }

    /**
     * @param array $data
     *
     * @return void
     *
     * @throws \UnexpectedValueException
     */
    private static function validateUnserializedData(array $data): void
    {
        /** Let's check if there are mandatory fields */
        if(
            false === isset($data['namespace']) ||
            false === isset($data['message'])
        )
        {
            throw new \UnexpectedValueException(
                'The serialized data must contains a "namespace" field (indicates the message class) and "message" (indicates the message parameters)'
            );
        }

        /** Let's check if the specified class exists */
        if('' === $data['namespace'] || false === \class_exists((string) $data['namespace']))
        {
            throw new \UnexpectedValueException(
                \sprintf('Class "%s" not found', $data['namespace'])
            );
        }
    }
}
