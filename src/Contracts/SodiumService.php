<?php

declare(strict_types=1);

namespace Healthlabs\Sodium\Contracts;

use Exception;
use Healthlabs\Sodium\Exceptions\DecryptException;
use Healthlabs\Sodium\Exceptions\KeyNotFoundException;
use Healthlabs\Sodium\Exceptions\MalformationException;

/**
 * Service contract that a implementation should follow.
 */
interface SodiumService
{
    /**
     * Encrypt the message. If the key parameter is not present, the default key will be used.
     *
     * @param string      $message the message that will be encrypted
     * @param string|null $nonce   a custom nonce used to encrypt the message
     * @param string|null $key     a custom key used to encrypt the message
     *
     * @throws KeyNotFoundException
     * @throws Exception
     */
    public function encrypt(string $message, string $nonce = null, string $key = null): string;

    /**
     * Decrypt the message. If the key parameter is not present, the default key will be used.
     *
     * @param string      $message the message that will be decrypted
     * @param string|null $key     a custom key used to decrypt the message
     *
     * @throws KeyNotFoundException
     * @throws MalformationException
     * @throws DecryptException
     */
    public function decrypt(string $message, string $key = null): string;
}
