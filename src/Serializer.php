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

/**
 *
 */
interface Serializer
{
    /**
     * Serialize data
     *
     * @param array<array-key, mixed> $payload
     *
     * @throws \Desperado\ServiceBus\MessageSerializer\Exceptions\SerializationFailed
     *
     * @return string
     */
    public function serialize(array $payload): string;

    /**
     * Unserialize data
     *
     * @param string $content
     *
     * @return array<array-key, mixed>
     *
     * @throws \Desperado\ServiceBus\MessageSerializer\Exceptions\UnserializeFailed
     */
    public function unserialize(string $content): array;
}
