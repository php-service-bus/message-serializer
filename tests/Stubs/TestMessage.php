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
final class TestMessage
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

    public static function create(string $componentName, ?string $stableVersion, string $devVersion, Author $author): self
    {
        return new self($componentName, $stableVersion, $devVersion, $author);
    }

    private function __construct(string $componentName, ?string $stableVersion, string $devVersion, Author $author)
    {
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->componentName = $componentName;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->stableVersion = $stableVersion;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->devVersion = $devVersion;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->author = $author;
    }
}
