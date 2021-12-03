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

final class EmptyClassWithPrivateConstructor
{
    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
    }
}
