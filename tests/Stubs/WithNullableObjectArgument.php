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
final class WithNullableObjectArgument implements Message
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var ClassWithPrivateConstructor|null
     */
    private $object;

    /**
     * @param string                      $value
     * @param ClassWithPrivateConstructor $object
     *
     * @return self
     */
    public static function withObject(string $value, ClassWithPrivateConstructor $object): self
    {
        return new self($value, $object);
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public static function withoutObject(string $value): self
    {
        return new self($value);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return ClassWithPrivateConstructor|null
     */
    public function getObject(): ?ClassWithPrivateConstructor
    {
        return $this->object;
    }

    /**
     * @param string                           $value
     * @param ClassWithPrivateConstructor|null $object
     */
    private function __construct(string $value, ?ClassWithPrivateConstructor $object = null)
    {
        $this->value  = $value;
        $this->object = $object;
    }
}
