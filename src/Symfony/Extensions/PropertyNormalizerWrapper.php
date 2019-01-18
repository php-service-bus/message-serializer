<?php

/**
 * PHP Service Bus (publish-subscribe pattern implementation) serializer component
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
}
