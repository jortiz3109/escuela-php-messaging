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

    protected $message_encryp = 'Hello World';
    protected $message_decryp = 'bm13NHBlQXAwb0pxT0d2Q2FKMkFadz09';

    public function testEncryptionValid()
    {
        $encryption = new Encryption($this->secret_key, $this->secret_iv, $this->encrypt_method, $this->algorith);
        $message_encrypted = $encryption->encrypt_openssl($this->message_encryp);
        $this->assertNotEmpty($message_encrypted);
    }

    public function testDecryptionValid()
    {
        $decryption = new Encryption($this->secret_key, $this->secret_iv, $this->encrypt_method, $this->algorith);
        $message_decrypted = $decryption->decrypt_openssl($this->message_decryp);
        $this->assertNotEmpty($message_decrypted);
    }

    public function testCorrectMessage()
    {
        $encryption = new Encryption($this->secret_key, $this->secret_iv, $this->encrypt_method, $this->algorith);
        $message_encrypted = $encryption->encrypt_openssl($this->message_encryp);

        $decryption = new Encryption($this->secret_key, $this->secret_iv, $this->encrypt_method, $this->algorith);
        $message_decrypted = $decryption->decrypt_openssl($message_encrypted);

        $this->assertSame($this->message_encryp, $message_decrypted);
    }
}