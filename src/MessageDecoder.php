<?php

/**
 * Messages serializer implementation
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
     * Restore message from string
     *
     * @param string $serializedMessage
     *
     * @return object
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed
     */
    public function decode(string $serializedMessage): object;

    /**
     * Convert array to specified object
     *
     * @param array<string, mixed> $payload
     * @param string               $class
     *
     * @return object
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed
     */
    public function denormalize(array $payload, string $class): object;
}