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
 */
final class WithPrivateProperties
{
    /**
     * @var string
     */
    private $qwerty;

    private int $root;
    private ?\DateTimeImmutable $dateTime;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    public function __construct(string $qwerty, int $root, \DateTimeImmutable $createdAt)
    {
        $this->qwerty    = $qwerty;
        $this->root      = $root;
        $this->createdAt = $createdAt;
    }
}
