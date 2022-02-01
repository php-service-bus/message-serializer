<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=0);

namespace ServiceBus\MessageSerializer;

interface ObjectSerializer
{
    /**
     * Encode message to string.
     *
     * @psalm-return non-empty-string
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\EncodeObjectFailed
     */
    public function encode(object $object): string;

    /**
     * Restore message from string.
     *
     * @template T of object
     * @psalm-param class-string<T> $objectClass
     * @psalm-param non-empty-string $serializedObject
     * @psalm-return T
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\DecodeObjectFailed
     */
    public function decode(string $serializedObject, string $objectClass): object;

    /**
     * Receive encoded data content type.
     *
     * @psalm-return non-empty-string
     */
    public function contentType(): string;
}
