<?php

/**
 * Messages serializer implementation
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\MessageSerializer;

use ServiceBus\MessageSerializer\Exceptions\SerializationFailed;
use ServiceBus\MessageSerializer\Exceptions\UnserializeFailed;

/**
 *
 */
final class JsonSerializer implements Serializer
{
    private const JSON_ERRORS_MAPPING = [
        \JSON_ERROR_DEPTH                 => 'The maximum stack depth has been exceeded',
        \JSON_ERROR_STATE_MISMATCH        => 'Invalid or malformed JSON',
        \JSON_ERROR_CTRL_CHAR             => 'Control character error, possibly incorrectly encoded',
        \JSON_ERROR_SYNTAX                => 'Syntax error',
        \JSON_ERROR_UTF8                  => 'Malformed UTF-8 characters, possibly incorrectly encoded',
        \JSON_ERROR_RECURSION             => 'One or more recursive references in the value to be encoded',
        \JSON_ERROR_INF_OR_NAN            => 'One or more NAN or INF values in the value to be encoded',
        \JSON_ERROR_UNSUPPORTED_TYPE      => 'A value of a type that cannot be encoded was given',
        \JSON_ERROR_INVALID_PROPERTY_NAME => 'A property name that cannot be encoded was given',
        \JSON_ERROR_UTF16                 => 'Malformed UTF-16 characters, possibly incorrectly encoded'
    ];

    /**
     * @inheritDoc
     */
    public function serialize(array $payload): string
    {
        /** Clear last error */
        \json_last_error();

        $encoded = \json_encode($payload);

        $lastResultCode = \json_last_error();

        if(false !== $encoded && \JSON_ERROR_NONE === $lastResultCode)
        {
            return $encoded;
        }

        throw new SerializationFailed(
            \sprintf(
                'JSON serialize failed: %s',
                self::JSON_ERRORS_MAPPING[$lastResultCode] ?? 'Unknown error'
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function unserialize(string $content): array
    {
        /** Clear last error */
        \json_last_error();

        /** @var array<string, string|int|float|null> $decoded */
        $decoded = \json_decode($content, true);

        $lastResultCode = \json_last_error();

        if(\JSON_ERROR_NONE === $lastResultCode)
        {
            return $decoded;
        }

        throw new UnserializeFailed(
            \sprintf(
                'JSON unserialize failed: %s',
                self::JSON_ERRORS_MAPPING[$lastResultCode] ?? 'Unknown error'
            )
        );
    }
}
