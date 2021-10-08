<?php

namespace Test\Unit;

use Tests\TestCase;
use Messaging\Utils\Encryption\Encryption;
use Tests\TestCase as TestsTestCase;

class EncryptionTest extends TestCase
{
    private $secret_key;
    private $secret_iv;
    private $encrypt_method;
    private $algorith;

    public function setUp(): void
    {
        $this->secret_key = file_get_contents(__DIR__, '/secret_key.pem');
        $this->secret_iv = file_get_contents(__DIR__, '/secret_iv.pem');
        $this->encrypt_method = file_get_contents(__DIR__, '/encrypt_method.pem');
        $this->algorith = file_get_contents(__DIR__, '/algorith.pem');
    }
}