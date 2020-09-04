<?php

declare(strict_types=1);

namespace Healthlabs\Sodium\Services;

use Exception;
use Healthlabs\Sodium\Contracts\SodiumService as Contract;
use Healthlabs\Sodium\Exceptions\DecryptException;
use Healthlabs\Sodium\Exceptions\KeyNotFoundException;
use Healthlabs\Sodium\Exceptions\MalformationException;
use Healthlabs\Sodium\Exceptions\NonceException;
use SodiumException;

/**
 * The service to encrypt/decrypt messages using sodium.
 */
class SodiumService implements Contract
{
    /** @var string|null The key to encrypt/decrypt message */
    protected $key;

    /**
     * SodiumService constructor.
     *
     * @param string|null $key the key to encrypt/decrypt the message
     */
    public function __construct(string $key = null)
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt(string $message, string $nonce = null, string $key = null): string
    {
        $nonce = $this->checkNonce($nonce);

        $key = $this->checkKey($key);

        $key = sodium_crypto_generichash($key, '', SODIUM_CRYPTO_GENERICHASH_BYTES);

        $encrypted = sodium_crypto_secretbox($message, $nonce, $key);

        return sprintf('%s.%s', sodium_bin2base64($nonce, SODIUM_BASE64_VARIANT_ORIGINAL), sodium_bin2base64($encrypted, SODIUM_BASE64_VARIANT_ORIGINAL));
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(string $message, string $key = null): string
    {
        $key = $this->checkKey($key);

        $payload = explode('.', $message);

        if (count($payload) !== 2) {
            throw new MalformationException('Decryption payload malformatted');
        }

        $decrypted = sodium_crypto_secretbox_open(
            sodium_base642bin($payload[1], SODIUM_BASE64_VARIANT_ORIGINAL),
            sodium_base642bin($payload[0], SODIUM_BASE64_VARIANT_ORIGINAL),
            sodium_crypto_generichash($key, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES)
        );

        if ($decrypted === false) {
            throw new DecryptException();
        }

        return $decrypted;
    }

    /**
     * Generate a random entropy used to encrypt the message.
     *
     * @param int $length the length of the entropy to generate
     *
     * @throws Exception
     */
    protected function entropy(int $length = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES): string
    {
        return random_bytes($length);
    }

    /**
     * Check if custom nonce meets the requirement, if not provided, generate a random nonce.
     *
     * @param string|null $nonce a custom nonce used to encrypt the message
     *
     * @throws NonceException
     * @throws Exception
     */
    protected function checkNonce(string $nonce = null): string
    {
        if ($nonce === null) {
            return $this->entropy();
        }

        if (strlen($nonce) !== SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new NonceException();
        }

        return $nonce;
    }

    /**
     * Check if key meets the requirement.
     *
     * @param string|null $key the key
     *
     * @throws KeyNotFoundException
     */
    protected function checkKey(string $key = null): string
    {
        if ($key !== null) {
            if ($key === '') {
                throw new KeyNotFoundException(KeyNotFoundException::CUSTOM_KEY_EMPTY_MESSAGE);
            }

            return $key;
        }

        if ($this->key !== null) {
            if ($this->key === '') {
                throw new KeyNotFoundException(KeyNotFoundException::DEFAULT_KEY_EMPTY_MESSAGE);
            }

            return $this->key;
        }

        throw new KeyNotFoundException(KeyNotFoundException::NEITHER_KEY_NOT_FOUND_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function decryptValueByNonce(string $value, string $nonce): ?string
    {
        try {
            return $this->decrypt($nonce ? implode('.', [$nonce, $value]) : $value);
        } catch (DecryptException | KeyNotFoundException | MalformationException | SodiumException $e) {
            return null;
        }
    }
}
