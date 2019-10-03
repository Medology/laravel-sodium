<?php declare(strict_types=1);

namespace Healthlabs\Sodium\Exceptions;

/**
 * The exception to be thrown when no working key is provided.
 */
class KeyNotFoundException extends SodiumException
{
    const DEFAULT_KEY_EMPTY_MESSAGE = 'The default key should not be an empty string';
    const CUSTOM_KEY_EMPTY_MESSAGE = 'The custom key should not be an empty string';
    const NEITHER_KEY_NOT_FOUND_MESSAGE = 'Neither default key nor custom key is not found';
}
