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

/**
 *
 */
interface Serializer
{
    /**
     * Serialize data.
     *
     * @psalm-param array<array-key, mixed> $payload
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\SerializationFailed
     */
    public function serialize(array $payload): string;

    /**
     * Unserialize data.
     *
     * @psalm-return array<array-key, mixed>
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\UnserializeFailed
     */
    public function unserialize(string $content): array;
}
