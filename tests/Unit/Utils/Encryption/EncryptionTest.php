<?php

namespace Test\Unit;

use Tests\TestCase;
use Messaging\Utils\Encryption\Encryption;

class EncryptionTest extends TestCase
{
    private $secretKey = 'CLASS-MESSAGE-KEY';
    private $encryptMethod = 'AES-256-CBC';
    private $algorithm = 'sha256';

    protected $message = 'Hello World';

    public function test_encryption_valid(): void
    {
        $encryption = new Encryption($this->secretKey, $this->encryptMethod, $this->algorithm);
        $messageEncrypted = $encryption->encrypt($this->message);
        $this->assertNotEmpty($messageEncrypted);
    }

    public function test_correct_message(): void
    {
        $encryption = new Encryption($this->secretKey, $this->encryptMethod, $this->algorithm);
        $messageEncrypted = $encryption->encrypt($this->message);

        $decryption = new Encryption($this->secretKey, $this->encryptMethod, $this->algorithm);
        $messageDecrypted = $decryption->decrypt($messageEncrypted);

        $this->assertSame($this->message, $messageDecrypted);
    }
}
