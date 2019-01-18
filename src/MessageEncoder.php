<?php

/**
 * PHP Service Bus (publish-subscribe pattern implementation) serializer component
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace Desperado\ServiceBus\MessageSerializer;

use Desperado\ServiceBus\Common\Messages\Message;

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
     * @throws \Desperado\ServiceBus\MessageSerializer\Exceptions\EncodeMessageFailed
     */
    public function encode(Message $message): string;

    /**
     * Convert object to array
     *
     * @param object $message
     *
     * @return array<string, mixed>
     *
     * @throws \Desperado\ServiceBus\MessageSerializer\Exceptions\NormalizationFailed Unexpected normalize result
     */
    public function normalize(object $message): array;
}
