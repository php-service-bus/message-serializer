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

use ServiceBus\MessageSerializer\Exceptions\DecodeObjectFailed;
use ServiceBus\MessageSerializer\Exceptions\DenormalizeFailed;
use ServiceBus\MessageSerializer\Exceptions\EncodeObjectFailed;
use ServiceBus\MessageSerializer\Exceptions\NormalizationFailed;
use ServiceBus\MessageSerializer\ObjectSerializer;
use ServiceBus\MessageSerializer\ObjectDenormalizer;
use ServiceBus\MessageSerializer\Symfony\Extensions\EmptyDataNormalizer;
use ServiceBus\MessageSerializer\Symfony\Extensions\PropertyNameConverter;
use ServiceBus\MessageSerializer\Symfony\Extensions\PropertyNormalizerWrapper;
use ServiceBus\MessageSerializer\Symfony\Extractor\CombinedExtractor;
use Symfony\Component\Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use function ServiceBus\Common\jsonDecode;
use function ServiceBus\Common\jsonEncode;

/**
 * @psalm-param array<array-key, Serializer\Normalizer\DenormalizerInterface|Serializer\Normalizer\NormalizerInterface>
 *              $customNormalizers
 *
 * @psalm-return array<array-key,
 *               Serializer\Normalizer\DenormalizerInterface|Serializer\Normalizer\NormalizerInterface>
 */
function normalizersPack(array $customNormalizers): array
{
    $defaultNormalizers = [
        new DateTimeNormalizer(['datetime_format' => 'c']),
        new Serializer\Normalizer\ArrayDenormalizer(),
        new PropertyNormalizerWrapper(
            classMetadataFactory: null,
            nameConverter: new PropertyNameConverter(),
            propertyTypeExtractor: new CombinedExtractor()
        ),
        new EmptyDataNormalizer(),
    ];

    /**
     * @noinspection PhpUnnecessaryLocalVariableInspection
     * @noinspection OneTimeUseVariablesInspection
     *
     * @psalm-var array<array-key, Serializer\Normalizer\DenormalizerInterface|Serializer\Normalizer\NormalizerInterface> $normalizers
     */
    $normalizers = \array_merge($customNormalizers, $defaultNormalizers);

    return $normalizers;
}
