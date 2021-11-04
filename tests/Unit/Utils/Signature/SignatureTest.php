<?php

namespace Tests\Unit\Utils\Signature;

use E4\Messaging\Exceptions\SignatureVerifyException;
use E4\Messaging\Exceptions\SignatureSignException;
use E4\Messaging\Utils\Signature\Signature;
use Tests\TestCase;

class SignatureTest extends TestCase
{
    private string $privateKey;
    private string $publicKey;
    private int    $algorithm = OPENSSL_ALGO_SHA256;
    private string $msgToSign = 'Mensaje a firmar';

    public function setUp(): void
    {
        $this->privateKey = file_get_contents(__DIR__ . '/../../../certs/signaturePrivateKey.pem');
        $this->publicKey = file_get_contents(__DIR__ . '/../../../certs/signaturePublicKey.pem');
    }

    public function test_it_shows_error_when_creating_signature_without_private_key(): void
    {
        $this->expectException(SignatureSignException::class);
        $this->expectExceptionMessage('Is necessary the private key');
        $signer = new Signature($this->algorithm, $this->publicKey);

        $signer->sign($this->msgToSign);
    }

    public function test_it_shows_error_when_creating_signature_with_incorrect_algorithm(): void
    {
        $this->expectException(SignatureSignException::class);
        $this->expectExceptionMessage('The correct algorithm is required');
        $signer = new Signature(OPENSSL_ALGO_MD5, $this->publicKey, $this->privateKey);

        $signer->sign($this->msgToSign);
    }

    public function test_it_create_signature_correctly(): void
    {
        $signer = new Signature($this->algorithm, $this->publicKey, $this->privateKey);
        $sign = $signer->sign('$this->msgToSign');

        $this->assertNotEmpty($sign);
    }

    public function test_it_return_false_when_the_signature_is_incorrect(): void
    {
        $signer = new Signature($this->algorithm, $this->publicKey);
        $sign = 'MEUCIQDEjlRMiAYyV0AsT0E9xtN7g2wZeWQO/mrfU5R85uEs6gIgN9/4dfpq4QG7kaOJ9s9Cpm74njKdJPB/O3MKeQgp0QI=';
        $res = $signer->verify($this->msgToSign, $sign);

        $this->assertFalse($res);
    }

    public function test_it_return_true_when_the_signature_is_correct(): void
    {
        $signer = new Signature($this->algorithm, $this->publicKey);
        $sign = 'MEYCIQC9vTCpwef4JaYcb1ub2Mpk1aUMo4eqEoC1jSa9ixll9gIhAPc2K7W8j3vl3AD73XItdQrdCUf970WkSIKrEAi0Fhvn';
        $res = $signer->verify($this->msgToSign, $sign);

        $this->assertTrue($res);
    }

    public function test_it_shows_error_when_validating_the_signature_with_wrong_algorithm(): void
    {
        $this->expectException(SignatureVerifyException::class);
        $signer = new Signature(OPENSSL_ALGO_MD5, $this->publicKey);
        $sign = 'MEUCIQDEjlRMiAYyV0AsT0E9xtN7g2wZeWQO/mrfU5R85uEs6gIgN9/4dfpq4QG7kaOJ9s9Cpm74njKdJPB/O3MKeQgp0QI=';

        $signer->verify($this->msgToSign, $sign);
    }
}
