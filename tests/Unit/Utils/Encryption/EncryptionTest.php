<?php

namespace Test\Unit;

use Tests\TestCase;
use Messaging\Utils\Encryption\Encryption;
use Tests\TestCase as TestsTestCase;

class EncryptionTest extends TestCase
{
    private $secret_key = 'CLASS-MESSAGE-KEY';
    private $secret_iv = 'CLASS-MESSAGE-VALUE';
    private $encrypt_method = 'AES-256-CBC';
    private $algorith = 'sha256';

    protected $message = 'Hello World';

    public function test_encryption_valid(): void
    {
        $encryption = new Encryption($this->secret_key, $this->secret_iv, $this->encrypt_method, $this->algorith);
        $message_encrypted = $encryption->encrypt($this->message);
        $this->assertNotEmpty($message_encrypted);
    }

    public function test_decryption_valid(): void
    {
        $decryption = new Encryption($this->secret_key, $this->secret_iv, $this->encrypt_method, $this->algorith);
        $message_decrypted = $decryption->decrypt($this->message);
        $this->assertNotEmpty($message_decrypted);
    }

    public function test_correct_message(): void
    {
        $encryption = new Encryption($this->secret_key, $this->secret_iv, $this->encrypt_method, $this->algorith);
        $message_encrypted = $encryption->encrypt($this->message);

        $decryption = new Encryption($this->secret_key, $this->secret_iv, $this->encrypt_method, $this->algorith);
        $message_decrypted = $decryption->decrypt($message_encrypted);

        $this->assertSame($this->message, $message_decrypted);
    }
}
