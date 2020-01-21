<?php

declare(strict_types=1);

namespace Healthlabs\Sodium\Exceptions;

use Throwable;

/**
 * The exception to be thrown when decryption failed.
 */
class NonceException extends SodiumException
{
    const message = 'The length of the nonce should be ' . SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct(empty($message) ? self::message : $message, $code, $previous);
    }
}
