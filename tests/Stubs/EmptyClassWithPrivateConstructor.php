<?php

/**
 * PHP Service Bus serializer component
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\Tests\Stubs;

use ServiceBus\Common\Messages\Message;

/**
 *
 */
final class EmptyClassWithPrivateConstructor implements Message
{
    /**
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {

    }
}
