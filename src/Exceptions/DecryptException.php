<?php declare(strict_types=1);
namespace Healthlabs\Sodium\Exceptions;

use Throwable;

/**
 * The exception to be thrown when decryption failed.
 */
class DecryptException extends SodiumException
{
    const message = 'Unable to decrypt the payload using the given key';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = self::message;
        }
        parent::__construct($message, $code, $previous);
    }
}