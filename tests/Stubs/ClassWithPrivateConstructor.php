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
final class ClassWithPrivateConstructor implements Message
{
    /**
     * @var string
     */
    private $value;

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
