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
use Symfony\Component\PropertyInfo\Type;

/**
 */
final class FailOverExtractor implements PropertyTypeExtractorInterface
{
    /**
     * @var PropertyTypeExtractorInterface[]
     */
    private array $extractors;

    public function __construct()
    {
        $this->extractors = [
            new ReflectionExtractor(),
            new PhpDocExtractor()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        foreach($this->extractors as $extractor)
        {
            $types = $extractor->getTypes($class, $property, $context);

            if(null !== $types)
            {
                return $types;
            }
        }

        return null;
    }
}
