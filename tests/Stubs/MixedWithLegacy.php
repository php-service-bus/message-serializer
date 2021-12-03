<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace ServiceBus\MessageSerializer\Tests\Stubs;

final class MixedWithLegacy
{
    /**
     * @var string
     */
    public $string;

    /**
     * @var \DateTimeInterface
     */
    public $dateTime;

    /**
     * @var int
     */
    public $long;

    /**
     * MixedWithLegacy constructor.
     *
     * @param string             $string
     * @param \DateTimeInterface $dateTime
     * @param int                $long
     */
    public function __construct(string $string, \DateTimeInterface $dateTime, int $long)
    {
        $this->string   = $string;
        $this->dateTime = $dateTime;
        $this->long     = $long;
    }
}
