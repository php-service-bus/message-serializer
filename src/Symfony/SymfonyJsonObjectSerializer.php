<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=0);

namespace ServiceBus\MessageSerializer\Symfony;

use ServiceBus\MessageSerializer\Exceptions\DecodeObjectFailed;
use ServiceBus\MessageSerializer\Exceptions\EncodeObjectFailed;
use ServiceBus\MessageSerializer\ObjectDenormalizer;
use ServiceBus\MessageSerializer\ObjectNormalizer;
use ServiceBus\MessageSerializer\ObjectSerializer;
use function ServiceBus\Common\jsonDecode;
use function ServiceBus\Common\jsonEncode;

final class SymfonyJsonObjectSerializer implements ObjectSerializer
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    /**
     * @var ObjectDenormalizer
     */
    private $denormalizer;

    public function __construct(ObjectNormalizer $normalizer = null, ObjectDenormalizer $denormalizer = null)
    {
        $this->normalizer   = $normalizer ?? new SymfonyObjectNormalizer();
        $this->denormalizer = $denormalizer ?? new SymfonyObjectDenormalizer();
    }

    public function encode(object $object): string
    {
        try
        {
            return jsonEncode(
                $this->normalizer->handle($object)
            );
        }
        catch (\Throwable $throwable)
        {
            throw new EncodeObjectFailed(
                \sprintf('Object `%s` serialization failed: %s', \get_class($object), $throwable->getMessage()),
                (int) $throwable->getCode(),
                $throwable
            );
        }
    }

    public function decode(string $serializedObject, string $objectClass): object
    {
        try
        {
            return $this->denormalizer->handle(
                payload: jsonDecode($serializedObject),
                objectClass: $objectClass
            );
        }
        catch (\Throwable $throwable)
        {
            throw new DecodeObjectFailed(
                \sprintf('Object `%s` deserialization failed: %s', $objectClass, $throwable->getMessage()),
                (int) $throwable->getCode(),
                $throwable
            );
        }
    }

    public function contentType(): string
    {
        return 'application/json';
    }
}
