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
interface MessageDecoder
{
    /**
     * Restore message from string.
     *
     * @param string $serializedMessage
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed
     *
     * @return object
     */
    public function decode(string $serializedMessage): object;

    /**
     * Convert array to specified object.
     *
     * @param array<string, mixed> $payload
     * @param string               $class
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed
     *
     * @return object
     */
    public function denormalize(array $payload, string $class): object;
}
