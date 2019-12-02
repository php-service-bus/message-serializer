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
    private array $localStorage = [];

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
    ): object {
        return $reflectionClass->newInstanceWithoutConstructor();
    }

    /**
     * {@inheritdoc}
     */
    protected function extractAttributes(object $object, string $format = null, array $context = []): array
    {
        $class = \get_class($object);

        if (false === isset($this->localStorage[$class]))
        {
            $this->localStorage[$class] = parent::extractAttributes($object, $format, $context);
        }

        return $this->localStorage[$class];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Throwable
     */
    protected function getAttributeValue(object $object, string $attribute, string $format = null, array $context = [])
    {
        if (true === isset($object->{$attribute}))
        {
            return $object->{$attribute};
        }

        try
        {
            return parent::getAttributeValue($object, $attribute, $format, $context);
        }
        catch (\Throwable $throwable)
        {
            if (\strpos($throwable->getMessage(), 'before initialization') !== false)
            {
                return null;
            }

            throw $throwable;
        }
    }

    /**
     * @psalm-param mixed $value
     *
     * {@inheritdoc}
     */
    protected function setAttributeValue(object $object, string $attribute, $value, string $format = null, array $context = []): void
    {
        if (true === isset($object->{$attribute}))
        {
            $object->{$attribute} = $value;
        }
        else
        {
            parent::setAttributeValue($object, $attribute, $value, $format, $context);
        }
    }
}
