<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=0);

namespace ServiceBus\MessageSerializer\Symfony;

use ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed;
use ServiceBus\MessageSerializer\ObjectDenormalizer;
use Symfony\Component\Serializer;

final class SymfonyObjectDenormalizer implements ObjectDenormalizer
{
    /**
     * Symfony normalizer\denormalizer.
     *
     * @var Serializer\Serializer
     */
    private $normalizer;

    /**
     * @psalm-param array<array-key, Serializer\Normalizer\DenormalizerInterface|Serializer\Normalizer\NormalizerInterface> $normalizers
     */
    public function __construct(array $normalizers = [])
    {
        $this->normalizer = new Serializer\Serializer(normalizersPack($normalizers));
    }

    /**
     * @template T of object
     * @psalm-param class-string<T> $objectClass
     * @psalm-return T
     */
    public function handle(array $payload, string $objectClass): object
    {
        try
        {
            /**
             * @noinspection PhpUnnecessaryLocalVariableInspection
             *
             * @psalm-var T $object
             */
            $object = $this->normalizer->denormalize(
                $payload,
                $objectClass
            );

            return $object;
        }
        catch (\Throwable $throwable)
        {
            throw new DenormalizeFailed($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }
}
