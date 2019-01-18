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
final class WithDateTimeField implements Message
{
    /**
     * @var \DateTimeImmutable
     */
    public $dateTimeValue;

    /**
     * @param \DateTimeImmutable $dateTimeValue
     */
    public function __construct(\DateTimeImmutable $dateTimeValue)
    {
        $this->dateTimeValue = $dateTimeValue;
    }
}
