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
     * @param array $payload
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\SerializationFailed
     *
     * @return string
     */
    public function serialize(array $payload): string;

    /**
     * Unserialize data.
     *
     * @psalm-return array<array-key, mixed>
     *
     * @param string $content
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\UnserializeFailed
     *
     * @return array
     */
    public function unserialize(string $content): array;
}
