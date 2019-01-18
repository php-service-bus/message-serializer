<?php

/**
 * PHP Service Bus (publish-subscribe pattern implementation) serializer component
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace Desperado\ServiceBus\MessageSerializer\Tests\Stubs;

use Desperado\ServiceBus\Common\Messages\Message;

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
