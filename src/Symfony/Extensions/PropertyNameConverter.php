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

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Convert snake_case to lowerCamelCase.
 */
final class PropertyNameConverter implements NameConverterInterface
{
    /**
     * Local cache.
     *
     * @psalm-var array<string, string>
     */
    private $localStorage = [];

    public function normalize(string $propertyName): string
    {
        return $propertyName;
    }

    public function denormalize(string $propertyName): string
    {
        if (isset($this->localStorage[$propertyName]) === false)
        {
            $joinedString = \preg_replace_callback(
                '/_(.?)/',
                static function (array $matches): string
                {
                    return \ucfirst((string) $matches[1]);
                },
                $propertyName
            );

            $this->localStorage[$propertyName] = \lcfirst((string) $joinedString);
        }

        return $this->localStorage[$propertyName];
    }
}
