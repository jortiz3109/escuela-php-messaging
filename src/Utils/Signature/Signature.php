<?php

namespace E4\Messaging\Utils\Signature;

use E4\Messaging\Utils\Signature\Exceptions\SignatureException;
use Throwable;

/**
 * Clase que permite firmar y verificar un mensaje
 * 
 * Comando para crear clave en Linux/MAC:
 * openssl ecparam -name secp256k1 -genkey -out privateKey.pem
 * openssl ec -in privateKey.pem -pubout -out publicKey.pem
 */
class Signature
{
    private string|null $privateKey;
    private string $publicKey;
    private int $algorithm;

    public function __construct(int $algorithm, string $publicKey, string|null $privateKey = null)
    {
        $this->algorithm  = $algorithm;
        $this->publicKey  = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * Crea la firma digital y la envía codificada en base64
     *
     * @param string $message
     *
     * @return string
     * @throws SignatureException
     * @throws Exception if the private key is wrong
     */
    public function sign(string $message): string
    {
        if (!$this->privateKey){
            throw new SignatureException('Is necessary the private key');
        }
        if (!openssl_sign($message, $signature, $this->privateKey, $this->algorithm)) {
            throw new SignatureException('The correct algorithm is required');
        }
        return base64_encode($signature);
    }

    /**
     * Verifica si una firma digital es correcta. Si la firma digital es erronea o el algoritmo de la firma no es
     * compactible con el de la llave publica, genera excepción
     *
     * @param string $message
     * @param string $signatureInBase64
     *
     * @return string
     * @throws SignatureException
     * @throws Exception if the public key is wrong
     */
    public function verify(string $message, string $signatureInBase64): mixed
    {
        $verified = openssl_verify($message, base64_decode($signatureInBase64), $this->publicKey, $this->algorithm);
        if ($verified == -1) {
            throw new SignatureException('The signature format or the signature algorithm is wrong');
        }
        return ($verified == 1);
    }
}
