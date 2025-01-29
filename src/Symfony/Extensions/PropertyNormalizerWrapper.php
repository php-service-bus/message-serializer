<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=0);

namespace ServiceBus\MessageSerializer\Symfony\Extensions;

use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * Disable the use of the constructor.
 *
 * @noinspection LongInheritanceChainInspection
 */
final class PropertyNormalizerWrapper extends AbstractObjectNormalizer
{
    private PropertyNormalizer $propertyNormalizer;

    /**
     * @psalm-var array<string, array<array-key, string>>
     */
    private array $localStorage = [];

    public function __construct(
        ?ClassMetadataFactoryInterface $classMetadataFactory = null,
        ?NameConverterInterface $nameConverter = null,
        ?PropertyTypeExtractorInterface $propertyTypeExtractor = null,
        ?ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null,
        ?callable $objectClassResolver = null,
        array $defaultContext = [],
    ) {
        parent::__construct(
            $classMetadataFactory,
            $nameConverter,
            $propertyTypeExtractor,
            $classDiscriminatorResolver,
            $objectClassResolver,
            $defaultContext
        );

        $this->propertyNormalizer = new PropertyNormalizer(
            $classMetadataFactory,
            $nameConverter,
            $propertyTypeExtractor,
            $classDiscriminatorResolver,
            $objectClassResolver,
            $defaultContext
        );
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function getSupportedTypes(?string $format): array
    {
        return ['object' => true];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->propertyNormalizer->supportsNormalization($data, $format, $context);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $this->propertyNormalizer->supportsDenormalization($data, $type, $format, $context);
    }

    /**
     * @throws \ReflectionException
     */
    protected function instantiateObject(
        array &$data,
        string $class,
        array &$context,
        \ReflectionClass $reflectionClass,
        array|bool $allowedAttributes,
        string $format = null,
    ): object {
        return $reflectionClass->newInstanceWithoutConstructor();
    }

    protected function isAllowedAttribute(
        object|string $classOrObject,
        string $attribute,
        ?string $format = null,
        array $context = [],
    ): bool {
        $reflMethod = new \ReflectionMethod($this->propertyNormalizer, 'isAllowedAttribute');

        /** @var bool $result */
        $result = $reflMethod->invoke($this->propertyNormalizer, $classOrObject, $attribute, $format, $context);

        return $result;
    }

    protected function extractAttributes(object $object, ?string $format = null, array $context = []): array
    {
        $class = \get_class($object);

        if (\array_key_exists($class, $this->localStorage) === false) {
            $reflMethod = new \ReflectionMethod($this->propertyNormalizer, 'extractAttributes');

            /** @var string[] $result */
            $result = $reflMethod->invoke(
                $this->propertyNormalizer,
                $object,
                $format,
                $context
            );

            $this->localStorage[$class] = $result;
        }

        return $this->localStorage[$class];
    }

    protected function getAttributeValue(
        object $object,
        string $attribute,
        ?string $format = null,
        array $context = [],
    ): mixed {
        $reflMethod = new \ReflectionMethod($this->propertyNormalizer, 'getAttributeValue');

        return $object->{$attribute} ?? $reflMethod->invoke(
            $this->propertyNormalizer,
            $object,
            $attribute,
            $format,
            $context
        );
    }

    protected function setAttributeValue(
        object $object,
        string $attribute,
        mixed $value,
        ?string $format = null,
        array $context = [],
    ): void {
        if (isset($object->{$attribute})) {
            $object->{$attribute} = $value;

            return;
        }

        $reflMethod = new \ReflectionMethod($this->propertyNormalizer, 'setAttributeValue');
        $reflMethod->invoke($this->propertyNormalizer, $object, $attribute, $value, $format, $context);
    }
}
