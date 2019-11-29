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
final class TestMessage
{
    private string $componentName;

    private ?string $stableVersion;

    private string $devVersion;

    private Author $author;

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
        $this->devVersion    = $devVersion;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->author        = $author;
    }
}
