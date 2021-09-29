<?php

namespace Messaging\Utils\Signature;

use Messaging\Utils\Signature\Exceptions\MissingPKException;

/**
 * Clase que permite firmar y verificar un mensaje usando el algoritmo SHA256
 * openssl ecparam -name secp256k1 -genkey -out privateKey.pem
 * openssl ec -in privateKey.pem -pubout -out publicKey.pem
 */

class Signature
{
    private $privateKey;
    private $publicKey;

    public function __construct(string $publicKey, string $privateKey = null)
    {
        $this->publicKey  = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * Crea la firma digital en base64
     * 
     * @param string $message
     * 
     * @return string
     */
    public function sign(string $message): string
    {
        if (!$this->privateKey){
            throw new MissingPKException('Is necesary the private key');
        }
        openssl_sign($message, $firma, $this->privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($firma);
    }

    /**
     * Verifica que la firma digital sea correcta
     * 
     * @param string $message
     * @param string $signatureInBase64
     * 
     * @return bool
     */
    public function verify(string $message, string $signatureInBase64): bool
    {
        $success = openssl_verify($message, base64_decode($signatureInBase64), $this->publicKey, OPENSSL_ALGO_SHA256);
        return ($success == 1);
    }
}