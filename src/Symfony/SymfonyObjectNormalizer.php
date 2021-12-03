<?php

declare(strict_types=1);

namespace ServiceBus\MessageSerializer\Symfony;

use ServiceBus\MessageSerializer\Exceptions\NormalizationFailed;
use ServiceBus\MessageSerializer\ObjectNormalizer;
use Symfony\Component\Serializer;

final class SymfonyObjectNormalizer implements ObjectNormalizer
{
    /**
     * Symfony normalizer\denormalizer.
     *
     * @var Serializer\Serializer
     */
    private $normalizer;

    /**
     * @psalm-param array<array-key,
     *              Serializer\Normalizer\DenormalizerInterface|Serializer\Normalizer\NormalizerInterface> $normalizers
     */
    public function __construct(array $normalizers = [])
    {
        $this->normalizer = new Serializer\Serializer(normalizersPack($normalizers));
    }

    public function handle(object $object): array
    {
        try
        {
            $data = $this->normalizer->normalize($object);

            if (\is_array($data))
            {
                /** @psalm-var array<array-key, mixed> $data */

                return $data;
            }

            // @codeCoverageIgnoreStart
            throw new \UnexpectedValueException(
                \sprintf(
                    'The normalization was to return the array. Type "%s" was obtained when object "%s" was normalized',
                    \gettype($data),
                    \get_class($object)
                )
            );
            // @codeCoverageIgnoreEnd
        }
        catch (\Throwable $throwable)
        {
            throw new NormalizationFailed($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }
}
