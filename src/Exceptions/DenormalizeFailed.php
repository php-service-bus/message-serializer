<?php

/**
 * Messages serializer implementation.
 *
 * @author  Maksim Masiukevich <contacts@desperado.dev>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 0);

namespace ServiceBus\MessageSerializer\Exceptions;

/**
 *
 */
final class DenormalizeFailed extends \RuntimeException implements SerializerExceptionMarker
{
}
