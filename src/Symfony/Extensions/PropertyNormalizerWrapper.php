<?php

/**
 * Messages serializer implementation
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\SymfonyNormalizer\Extensions;

use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * Disable the use of the constructor
 *
 * @noinspection LongInheritanceChainInspection
 */
final class PropertyNormalizerWrapper extends PropertyNormalizer
{
    /**
     * @var array<string, array<array-key, string>>
     */
    private $localStorage = [];

    /**
     * @inheritdoc
     *
     * @psalm-suppress MissingParamType Cannot specify data type
     */
    protected function instantiateObject(
        array &$data,
        $class,
        array &$context,
        \ReflectionClass $reflectionClass,
        $allowedAttributes,
        string $format = null
    ): object
    {
        return $reflectionClass->newInstanceWithoutConstructor();
    }

    /**
     * @inheritdoc
     *
     * @param object      $object
     * @param string|null $format
     * @param array       $context
     */
    protected function extractAttributes($object, $format = null, array $context = []): array
    {
        $class = \get_class($object);

        if(false === isset($this->localStorage[$class]))
        {
            $this->localStorage[$class] = parent::extractAttributes($object, $format, $context);
        }

        return $this->localStorage[$class];
    }
}
