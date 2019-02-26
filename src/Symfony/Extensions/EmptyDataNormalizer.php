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

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer for an object without attributes (empty)
 */
final class EmptyDataNormalizer implements NormalizerInterface
{
    /**
     * @psalm-var array<string, array<array-key, string>>
     * @var array
     */
    private $localStorage = [];

    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @throws \ReflectionException
     */
    public function supportsNormalization($data, $format = null): bool
    {
        if(true === \is_object($data))
        {
            $class = \get_class($data);

            if(false === isset($this->localStorage[$class]))
            {
                $this->localStorage[$class] = \array_map(
                    static function(\ReflectionProperty $property): string
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

}
