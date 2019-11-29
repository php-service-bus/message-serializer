<?php

/**
 * Messages serializer implementation.
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
final class ClassWithPrivateConstructor
{
    private string $value;

    /**
     * @param mixed $value
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
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->value = $value;
    }
}
