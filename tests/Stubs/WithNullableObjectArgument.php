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

final class WithNullableObjectArgument
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var ClassWithPrivateConstructor|null
     */
    private $object;

    public static function withObject(string $value, ClassWithPrivateConstructor $object): self
    {
        return new self($value, $object);
    }

    public static function withoutObject(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getObject(): ?ClassWithPrivateConstructor
    {
        return $this->object;
    }

    private function __construct(string $value, ?ClassWithPrivateConstructor $object = null)
    {
        $this->value  = $value;
        $this->object = $object;
    }
}
