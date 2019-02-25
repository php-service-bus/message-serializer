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
 * Encoding a message into a string
 */
interface MessageEncoder
{
    /**
     * Encode message to string
     *
     * @param object $message
     *
     * @return string
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\EncodeMessageFailed
     */
    public function encode(object $message): string;

    /**
     * Convert object to array
     *
     * @param object $message
     *
     * @return array<string, mixed>
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\NormalizationFailed Unexpected normalize result
     */
    public function normalize(object $message): array;
}
