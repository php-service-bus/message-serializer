<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\SymfonyNormalizer\Extensions;

use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * Disable the use of the constructor.
 *
 * @noinspection LongInheritanceChainInspection
 */
final class PropertyNormalizerWrapper extends PropertyNormalizer
{
    /**
     * @psalm-var array<string, array<array-key, string>>
     */
    private array

        $localStorage = [];

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function extractAttributes(object $object, string $format = null, array $context = []): array
    {
        $class = \get_class($object);

        if(false === isset($this->localStorage[$class]))
        {
            $this->localStorage[$class] = [];

            foreach(\get_object_vars($object) as $key => $value)
            {
                $this->localStorage[$class][] = $key;
            }
        }

        return $this->localStorage[$class];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeValue(object $object, string $attribute, string $format = null, array $context = [])
    {
        if(isset($object->{$attribute}) === true)
        {
            return $object->{$attribute};
        }

        return parent::getAttributeValue($object, $attribute, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    protected function setAttributeValue(object $object, string $attribute, $value, string $format = null, array $context = [])
    {
        if(isset($object->{$attribute}) === true)
        {
            $object->{$attribute} = $value;
        }
        else
        {
            parent::setAttributeValue($object, $attribute, $value, $format, $context);
        }
    }
}
