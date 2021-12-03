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

/**
 * Encoding a message into a string.
 */
interface ObjectNormalizer
{
    /**
     * Convert object to array.
     *
     * @psalm-return array<array-key, mixed>
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\NormalizationFailed Unexpected normalize result
     */
    public function handle(object $object): array;
}
