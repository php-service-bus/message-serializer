<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=0);

namespace ServiceBus\MessageSerializer\Symfony\Extractor;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;

final class CombinedExtractor implements PropertyTypeExtractorInterface
{
    /**
     * @psalm-var array<string, \Symfony\Component\PropertyInfo\Type[]|null>
     */
    private $localStorage = [];

    /**
     * @var PhpDocExtractor
     */
    private $phpDocExtractor;

    /**
     * @var ReflectionExtractor
     */
    private $reflectionPropertyExtractor;

    public function __construct()
    {
        $this->phpDocExtractor             = new PhpDocExtractor();
        $this->reflectionPropertyExtractor = new ReflectionExtractor();
    }

    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        $cacheKey = $class . $property;

        if (\array_key_exists($cacheKey, $this->localStorage) === false)
        {
            $types = $this->phpDocExtractor->getTypes($class, $property, $context);

            if ($types === null)
            {
                $types = $this->reflectionPropertyExtractor->getTypes($class, $property, $context);
            }

            $this->localStorage[$cacheKey] = $types;
        }

        return $this->localStorage[$cacheKey];
    }
}
