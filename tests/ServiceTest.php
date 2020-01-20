<?php

declare(strict_types=1);

namespace Healthlabs\Sodium\Tests;

use Healthlabs\Sodium\Exceptions\DecryptException;
use Healthlabs\Sodium\Exceptions\KeyNotFoundException;
use Healthlabs\Sodium\Exceptions\NonceException;
use Healthlabs\Sodium\Exceptions\SodiumException;
use Healthlabs\Sodium\Services\SodiumService;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    /**
     * Encrypt and decrypt the message successfully with custom key.
     *
     * @throws SodiumException
     */
    public function testEncryptAndDecryptSuccessfulUsingCustomKey()
    {
        $service = new SodiumService();
        $message = 'test_message';
        $key = 'test_key';
        $encrypted = $service->encrypt($message, null, $key);
        $decrypted = $service->decrypt($encrypted, $key);
        $this->assertNotEquals($message, $encrypted);
        $this->assertEquals($message, $decrypted);
    }

    /**
     * Encrypt and decrypt the message successfully with service key.
     *
     * @throws DecryptException
     * @throws KeyNotFoundException
     * @throws SodiumException
     */
    public function testEncryptAndDecryptSuccessfulUsingServiceKey()
    {
        $service = new SodiumService('test_key');
        $message = 'test_message';
        $encrypted = $service->encrypt($message);
        $decrypted = $service->decrypt($encrypted);
        $this->assertNotEquals($message, $encrypted);
        $this->assertEquals($message, $decrypted);
    }

    /**
     * Exception should thrown when a different key is used to decrypt the message.
     *
     * @throws SodiumException
     */
    public function testEncryptAndDecryptFailedWithWrongKey()
    {
        $service = new SodiumService();
        $message = 'test_message';
        $key = 'test_key';
        $wrongKey = 'wrong_key';
        $encrypted = $service->encrypt($message, null, $key);
        $this->expectException(DecryptException::class);
        $this->expectExceptionMessage(DecryptException::message);
        $service->decrypt($encrypted, $wrongKey);
    }

    /**
     * Proper exception should throw for different key present scenarios when encrypting.
     *
     * @param string|null $defaultKey       the default key
     * @param string|null $customKey        the custom key
     * @param string      $exception        the exception that is expected
     * @param string      $exceptionMessage the exception message that is expected
     *
     * @throws KeyNotFoundException
     * @dataProvider keyPresenceDataProvider
     */
    public function testKeyPresenceExceptionWhenForEncrypt(
        ?string $defaultKey,
        ?string $customKey,
        string $exception,
        string $exceptionMessage
    ) {
        $service = new SodiumService($defaultKey);
        $message = 'test_message';

        $this->expectException($exception);
        $this->expectExceptionMessage($exceptionMessage);
        $service->encrypt($message, null, $customKey);
    }

    /**
     * Proper exception should throw for different key present scenarios when decrypting.
     *
     * @param string|null $defaultKey       the default key
     * @param string|null $customKey        the custom key
     * @param string      $exception        the exception that is expected
     * @param string      $exceptionMessage the exception message that is expected
     *
     * @throws SodiumException
     * @dataProvider keyPresenceDataProvider
     */
    public function testKeyPresenceExceptionWhenForDecrypt(
        ?string $defaultKey,
        ?string $customKey,
        string $exception,
        string $exceptionMessage
    ) {
        $service = new SodiumService($defaultKey);
        $message = 'test_message';

        $this->expectException($exception);
        $this->expectExceptionMessage($exceptionMessage);
        $service->decrypt($message, $customKey);
    }

    /**
     * Data provider for the key presence tests.
     */
    public function keyPresenceDataProvider(): array
    {
        return [
            'both key missing' => [
                'default key' => null,
                'custom key'  => null,
                'exception'   => KeyNotFoundException::class,
                'message'     => KeyNotFoundException::NEITHER_KEY_NOT_FOUND_MESSAGE,
            ],
            'customer key is empty string' => [
                'default key' => null,
                'custom key'  => '',
                'exception'   => KeyNotFoundException::class,
                'message'     => KeyNotFoundException::CUSTOM_KEY_EMPTY_MESSAGE,
            ],
            'default key is empty string while custom key is not provided' => [
                'default key' => '',
                'custom key'  => null,
                'exception'   => KeyNotFoundException::class,
                'message'     => KeyNotFoundException::DEFAULT_KEY_EMPTY_MESSAGE,
            ],
        ];
    }

    /**
     * Encrypt and decrypt the message successfully with custom nonce.
     */
    public function testEncryptAndDecryptSuccessfulUsingCustomNonce()
    {
        $service = new SodiumService();
        $message = 'test_message';
        $key = 'test_key';
        $nonce = 'abcdefghijklmnopqrstuvwx';
        $encrypted = $service->encrypt($message, $nonce, $key);
        $decrypted = $service->decrypt($encrypted, $key);
        $this->assertNotEquals($message, $encrypted);
        $this->assertEquals($message, $decrypted);
    }

    /**
     * Exception should throw when customer nonce is present but doesn't meet the requirement.
     *
     * @throws SodiumException
     */
    public function testExceptionShouldThrownWhenCustomNonceDoesNotMeetRequirement()
    {
        $service = new SodiumService();
        $message = 'test_message';
        $key = 'test_key';
        $nonce23Char = 'abcdefghijklmnopqrstuvw';
        $nonce25Char = 'abcdefghijklmnopqrstuvwxy';
        $this->expectException(NonceException::class);
        $this->expectExceptionMessage(NonceException::message);
        $service->encrypt($message, $nonce23Char, $key);
        $this->expectException(NonceException::class);
        $this->expectExceptionMessage(NonceException::message);
        $service->encrypt($message, $nonce25Char, $key);
    }
}
