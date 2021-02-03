<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 0);

namespace ServiceBus\MessageSerializer;

/**
 *
 */
interface MessageDecoder
{
    /**
     * Restore message from string.
     *
     * @template T
     * @psalm-param class-string<T> $messageClass
     * @psalm-return T
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\DecodeMessageFailed
     */
    public function decode(string $serializedMessage, string $messageClass): object;

    /**
     * Convert array to specified object.
     *
     * @template T
     * @psalm-param array<string, mixed> $payload
     * @psalm-param class-string<T>      $messageClass
     * @psalm-return T
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed
     */
    public function denormalize(array $payload, string $messageClass): object;
}
