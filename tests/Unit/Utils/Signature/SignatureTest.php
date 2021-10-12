<?php

namespace Tests\Unit;

use Tests\TestCase;
use E4\Messaging\Utils\Signature\Signature;
use E4\Messaging\Utils\Signature\Exceptions\MissingPKException;

class SignatureTest extends TestCase
{
    private string $privateKey;
    private string $publicKey;
    private static int $algorithm    = OPENSSL_ALGO_SHA256;
    private static string $msgToSign = 'Mensaje a firmar';

    public function setUp(): void
    {
        $this->privateKey = file_get_contents(__DIR__ . '/privateKey.pem');
        $this->publicKey  = file_get_contents(__DIR__ . '/publicKey.pem');
    }

    public function test_it_shows_error_when_creating_signature_without_private_key(): void
    {
        $this->expectException(MissingPKException::class);
        $this->expectExceptionMessage('Is necesary the private key');
        $signer = new Signature(self::$algorithm, $this->publicKey);

        $signer->sign(self::$msgToSign);
    }

    public function test_it_shows_error_when_creating_signature_with_incorrect_algorithm(): void
    {
        $this->expectException(MissingPKException::class);
        $this->expectExceptionMessage('The correct algorithm is required');
        $signer = new Signature(OPENSSL_ALGO_MD5, $this->publicKey, $this->privateKey);

        $signer->sign(self::$msgToSign);
    }

    public function test_it_create_signature_correctly(): void
    {
        $signer = new Signature(self::$algorithm, $this->publicKey, $this->privateKey);
        $sign   = $signer->sign(self::$msgToSign);

        $this->assertNotEmpty($sign);
    }

    public function test_it_return_false_when_the_signature_is_incorrect(): void
    {
        $signer = new Signature(self::$algorithm, $this->publicKey);
        $sign   = 'FIRMA_INCORRECTA';
        $res    = $signer->verify(self::$msgToSign, $sign);

        $this->assertFalse($res);
    }

    public function test_it_return_true_when_the_signature_is_correct(): void
    {
        $signer = new Signature(self::$algorithm, $this->publicKey);
        $sign   = 'MEYCIQC9vTCpwef4JaYcb1ub2Mpk1aUMo4eqEoC1jSa9ixll9gIhAPc2K7W8j3vl3AD73XItdQrdCUf970WkSIKrEAi0Fhvn';
        $res    = $signer->verify(self::$msgToSign, $sign);

        $this->assertTrue($res);
    }
}
