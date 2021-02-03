<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 0);

namespace ServiceBus\MessageSerializer\Symfony\Extensions;

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
    private $localStorage = [];

    /**
     * {@inheritdoc}
     *
     * @param array|bool $allowedAttributes
     *
     * @throws \ReflectionException
     * @noinspection PhpMissingParamTypeInspection
     */
    protected function instantiateObject(
        array &$data,
        string $class,
        array &$context,
        \ReflectionClass $reflectionClass,
        $allowedAttributes,
        string $format = null
    ): object {
        return $reflectionClass->newInstanceWithoutConstructor();
    }

    /**
     * {@inheritdoc}
     */
    protected function extractAttributes(object $object, string $format = null, array $context = []): array
    {
        $class = \get_class($object);

        if (\array_key_exists($class, $this->localStorage) === false)
        {
            $this->localStorage[$class] = parent::extractAttributes($object, $format, $context);
        }

        return $this->localStorage[$class];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Error
     */
    protected function getAttributeValue(object $object, string $attribute, string $format = null, array $context = [])
    {
        return $object->{$attribute} ?? parent::getAttributeValue($object, $attribute, $format, $context);
    }

    /**
     * @psalm-param mixed $value
     *
     * {@inheritdoc}
     */
    protected function setAttributeValue(object $object, string $attribute, $value, string $format = null, array $context = []): void
    {
        if (isset($object->{$attribute}))
        {
            $object->{$attribute} = $value;

            return;
        }

        parent::setAttributeValue($object, $attribute, $value, $format, $context);
    }
}
