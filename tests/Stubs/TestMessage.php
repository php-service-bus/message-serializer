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
        $this->componentName = $componentName;
        $this->stableVersion = $stableVersion;
        $this->devVersion    = $devVersion;
        $this->author        = $author;
    }
}
