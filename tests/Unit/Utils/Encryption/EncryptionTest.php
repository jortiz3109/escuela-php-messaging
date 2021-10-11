<?php

namespace Test\Unit;

use Tests\TestCase;
use E4\Messaging\Utils\Encryption\Encryption;
use Tests\TestCase as TestsTestCase;

class EncryptionTest extends TestCase
{
    private $secretKey = 'CLASS-MESSAGE-KEY';
    private $secretIv = 'CLASS-MESSAGE-VALUE';
    private $encryptMethod = 'AES-256-CBC';
    private $algorith = 'sha256';

    protected $message = 'Hello World';

    public function test_encryption_valid(): void
    {
        $encryption = new Encryption($this->secretKey, $this->secretIv, $this->encryptMethod, $this->algorith);
        $messageEncrypted = $encryption->encrypt($this->message);
        $this->assertNotEmpty($messageEncrypted);
    }

    public function test_decryption_valid(): void
    {
        $decryption = new Encryption($this->secretKey, $this->secretIv, $this->encryptMethod, $this->algorith);
        $messageDecrypted = $decryption->decrypt($this->message);
        $this->assertNotEmpty($messageDecrypted);
    }

    public function test_correct_message(): void
    {
        $encryption = new Encryption($this->secretKey, $this->secretIv, $this->encryptMethod, $this->algorith);
        $messageEncrypted = $encryption->encrypt($this->message);

        $decryption = new Encryption($this->secretKey, $this->secretIv, $this->encryptMethod, $this->algorith);
        $messageDecrypted = $decryption->decrypt($messageEncrypted);

        $this->assertSame($this->message, $messageDecrypted);
    }
}
