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
final class Author
{
    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @param string $firstName
     * @param string $lastName
     *
     * @return self
     */
    public static function create(string $firstName, string $lastName): self
    {
        return new self($firstName, $lastName);
    }

    /**
     * @param string $firstName
     * @param string $lastName
     */
    private function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
    }
}
