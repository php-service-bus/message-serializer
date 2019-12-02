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
use function ServiceBus\Common\jsonDecode;
use function ServiceBus\Common\jsonEncode;

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
            return jsonEncode($payload);
        }
        catch (\Throwable $throwable)
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
            return jsonDecode($content);
        }
        catch (\Throwable $throwable)
        {
            throw new UnserializeFailed(
                \sprintf('JSON unserialize failed: %s', $throwable->getMessage()),
                (int) $throwable->getCode(),
                $throwable
            );
        }
    }
}
