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

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer for an object without attributes (empty)
 */
final class EmptyDataNormalizer implements NormalizerInterface
{
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
            return 0 === \count((new \ReflectionClass($data))->getProperties());
        }

        return false;
    }
}
