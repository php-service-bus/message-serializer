<?php

/**
 * Messages serializer implementation
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\Tests\Stubs;

/**
 *
 */
final class WithDateTimeField
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
