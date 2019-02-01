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

use function ServiceBus\Common\createWithoutConstructor;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizer for an object without attributes (empty)
 */
final class EmptyDataDenormalizer implements DenormalizerInterface
{
    /**
     * @noinspection MoreThanThreeArgumentsInspection
     *
     * {@inheritdoc}
     *
     * @throws \ServiceBus\Common\Exceptions\Reflection\ReflectionClassNotFound
     */
    public function denormalize($data, $class, $format = null, array $context = []): object
    {
        return createWithoutConstructor($class);
    }

    /**
     * @noinspection MoreThanThreeArgumentsInspection
     *
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return \is_array($data) && 0 === \count($data);
    }
}
