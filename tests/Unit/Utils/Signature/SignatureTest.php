<?php

namespace Tests\Unit;

use Tests\TestCase;
use Messaging\Utils\Signature\Signature;
use Messaging\Utils\Signature\Exceptions\MissingPKException;

class SignatureTest extends TestCase
{
    private $privateKey;
    private $publicKey;

    public function setUp(): void
    {
        $this->privateKey = file_get_contents(__DIR__ . '/privateKey.pem');
        $this->publicKey  = file_get_contents(__DIR__ . '/publicKey.pem');
    }

    /**
     * Verifica que muestre error al crear una firma sin clave privada
     *
     * @test
     */
    public function it_shows_error_when_creating_signature_without_private_key()
    {
        $this->expectException(MissingPKException::class);
        $signer = new Signature($this->publicKey);
        $signer->sing('Mensaje conenido');
    }

    /**
     * Verifica que crea firma digital de forma correcta
     *
     * @test
     */
    public function it_create_digital_signature_correctly()
    {
        $signer = new Signature($this->publicKey, $this->privateKey);
        $sign   = $signer->sing('Mensaje a firmar');
        $this->assertNotEmpty($sign);
    }

    /**
     * Verifica que retorna falso cuando la firma es incorrecta
     *
     * @test
     */
    public function it_return_false_when_the_signature_is_incorrect()
    {
        $signer = new Signature($this->publicKey);
        $sign   = 'FIRMA_INCORRECTA';
        $res    = $signer->verify('Mensaje contenido v3', $sign);
        $this->assertFalse($res);
    }

    /**
     * Verifica una firma digital de forma correcta
     *
     * @test
     */
    public function it_return_true_when_the_signature_is_correct()
    {
        $signer = new Signature($this->publicKey);
        $sign   = 'MEYCIQC9vTCpwef4JaYcb1ub2Mpk1aUMo4eqEoC1jSa9ixll9gIhAPc2K7W8j3vl3AD73XItdQrdCUf970WkSIKrEAi0Fhvn';
        $res    = $signer->verify('Mensaje a firmar', $sign);
        $this->assertTrue($res);
    }
}
