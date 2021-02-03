<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\Tests\Stubs;

/**
 *
 */
final class ClassWithPrivateConstructor
{
    /**
     * @var string
     */
    public $value;

    /**
     * @param mixed
     *
     * @return self
     */
    public static function create($value): self
    {
        return new self($value);
    }

    /**
     * @param mixed $value
     */
    private function __construct($value)
    {
        $this->value = $value;
    }
}
