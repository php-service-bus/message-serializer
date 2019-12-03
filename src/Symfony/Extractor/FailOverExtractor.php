<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\Symfony\Extractor;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;

/**
 */
final class FailOverExtractor implements PropertyTypeExtractorInterface
{
    /**
     * @psalm-var array<string, \Symfony\Component\PropertyInfo\Type[]|null>
     */
    private $localStorage = [];

    /**
     * @var PropertyTypeExtractorInterface[]
     */
    private $extractors;

    public function __construct()
    {
        $this->extractors = [new PhpDocExtractor(), new ReflectionExtractor()];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        $cacheKey = \sha1($class . $property);

        if (\array_key_exists($cacheKey, $this->localStorage) === false)
        {
            $this->localStorage[$cacheKey] = null;

            foreach ($this->extractors as $extractor)
            {
                $types = $extractor->getTypes($class, $property, $context);

                if (null !== $types)
                {
                    $this->localStorage[$cacheKey] = $types;

                    break;
                }
            }
        }

        return $this->localStorage[$cacheKey];
    }
}
