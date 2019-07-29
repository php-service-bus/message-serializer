<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer;

use ServiceBus\MessageSerializer\Exceptions\SerializationFailed;
use ServiceBus\MessageSerializer\Exceptions\UnserializeFailed;

/**
 *
 */
final class JsonSerializer implements Serializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(array $payload): string
    {
        try
        {
            /** @var string $encoded */
            $encoded = \json_encode($payload, \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);

            return $encoded;
        }
        catch(\Throwable $throwable)
        {
            throw new SerializationFailed(
                \sprintf('JSON serialize failed: %s', $throwable->getMessage()),
                (int) $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize(string $content): array
    {
        try
        {
            /** @psalm-var array<string, string|int|float|null> $decoded */
            $decoded = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

            return $decoded;
        }
        catch(\Throwable $throwable)
        {
            throw new UnserializeFailed(
                \sprintf('JSON unserialize failed: %s', $throwable->getMessage()),
                (int) $throwable->getCode(),
                $throwable
            );
        }
    }
}
