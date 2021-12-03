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

interface ObjectDenormalizer
{
    /**
     * Convert array to specified object.
     *
     * @template T of object
     * @psalm-param array<array-key, mixed> $payload
     * @psalm-param class-string<T>         $objectClass
     * @psalm-return T
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed
     */
    public function handle(array $payload, string $objectClass): object;
}
