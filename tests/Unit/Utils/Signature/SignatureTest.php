<?php

namespace Tests\Unit;

use Tests\TestCase;
use E4\Messaging\Utils\Signature\Signature;
use E4\Messaging\Utils\Signature\Exceptions\MissingPKException;

class SignatureTest extends TestCase
{
    private string $privateKey;
    private string $publicKey;
    private string $algorithm;
    private string $msgToSign = 'Mensaje a firmar';

    public function setUp(): void
    {
        $this->privateKey = file_get_contents(__DIR__ . '/privateKey.pem');
        $this->publicKey  = file_get_contents(__DIR__ . '/publicKey.pem');
        $this->algorithm  = OPENSSL_ALGO_SHA256;
    }

    /**
     * Verifica que muestre error al crear una firma sin clave privada
     *
     * @return void
     */
    public function test_it_shows_error_when_creating_signature_without_private_key(): void
    {
        $this->expectException(MissingPKException::class);
        $signer = new Signature($this->algorithm, $this->publicKey);
        $signer->sign($this->msgToSign);
    }

    /**
     * Verifica que crea firma digital de forma correcta
     *
     * @return void
     */
    public function test_it_create_signature_correctly(): void
    {
        $signer = new Signature($this->algorithm, $this->publicKey, $this->privateKey);
        $sign   = $signer->sign($this->msgToSign);
        $this->assertNotEmpty($sign);
    }

    /**
     * Verifica que retorna falso cuando la firma es incorrecta
     *
     * @return void
     */
    public function test_it_return_false_when_the_signature_is_incorrect(): void
    {
        $signer = new Signature($this->algorithm, $this->publicKey);
        $sign   = 'FIRMA_INCORRECTA';
        $res    = $signer->verify($this->msgToSign, $sign);
        $this->assertFalse($res);
    }

    /**
     * Verifica una firma digital de forma correcta
     *
     * @return void
     */
    public function test_it_return_true_when_the_signature_is_correct(): void
    {
        $signer = new Signature($this->algorithm, $this->publicKey);
        $sign   = 'MEYCIQC9vTCpwef4JaYcb1ub2Mpk1aUMo4eqEoC1jSa9ixll9gIhAPc2K7W8j3vl3AD73XItdQrdCUf970WkSIKrEAi0Fhvn';
        $res    = $signer->verify($this->msgToSign, $sign);
        $this->assertTrue($res);
    }
}
