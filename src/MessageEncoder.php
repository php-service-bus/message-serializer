<?php

/**
 * PHP Service Bus (publish-subscribe pattern implementation) serializer component
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer;

use ServiceBus\Common\Messages\Message;

/**
 * Encoding a message into a string
 */
interface MessageEncoder
{
    /**
     * Encode message to string
     *
     * @param Message $message
     *
     * @return string
     *
     * @throws \ServiceBus\MessageSerializer\Exceptions\EncodeMessageFailed
     */
    public function encode(Message $message): string;

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
