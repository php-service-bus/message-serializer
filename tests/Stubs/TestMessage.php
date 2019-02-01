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

use ServiceBus\Common\Messages\Message;

/**
 *
 */
final class TestMessage implements Message
{
    /**
     * @var string
     */
    private $componentName;

    /**
     * @var string|null
     */
    private $stableVersion;

    /**
     * @var string
     */
    private $devVersion;

    /**
     * @var Author
     */
    private $author;

    /**
     * @param string      $componentName
     * @param string|null $stableVersion
     * @param string      $devVersion
     * @param Author      $author
     *
     * @return TestMessage
     */
    public static function create(string $componentName, ?string $stableVersion, string $devVersion, Author $author): self
    {
        return new self($componentName, $stableVersion, $devVersion, $author);
    }

    /**
     * @param string      $componentName
     * @param string|null $stableVersion
     * @param string      $devVersion
     * @param Author      $author
     */
    private function __construct(string $componentName, ?string $stableVersion, string $devVersion, Author $author)
    {
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->componentName = $componentName;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->stableVersion = $stableVersion;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->devVersion    = $devVersion;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->author        = $author;
    }
}
