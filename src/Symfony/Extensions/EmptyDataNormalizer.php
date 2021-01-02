<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer\Symfony\Extensions;

use function ServiceBus\Common\createWithoutConstructor;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer for an object without attributes (empty).
 */
final class EmptyDataNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @psalm-var array<string, array<array-key, string>>
     */
    private $localStorage = [];

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \ReflectionException
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        if (\is_object($data))
        {
            $class = \get_class($data);

            if (isset($this->localStorage[$class]) === false)
            {
                $this->localStorage[$class] = \array_map(
                    static function (\ReflectionProperty $property): string
                    {
                        return (string) $property->name;
                    },
                    (new \ReflectionClass($data))->getProperties()
                );
            }

            return empty($this->localStorage[$class]);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \ServiceBus\Common\Exceptions\ReflectionApiException
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): object
    {
        /** @psalm-var class-string $type */

        return createWithoutConstructor($type);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return empty($data);
    }
}
