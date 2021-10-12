<?php

namespace E4\Messaging\Utils\Signature;

use E4\Messaging\Utils\Signature\Exceptions\MissingPKException;

/**
 * Clase que permite firmar y verificar un mensaje usando el algoritmo SHA256
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
        $this->algorithm   = $algorithm;
        $this->publicKey  = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * Crea la firma digital en base64
     *
     * @param string $message
     *
     * @return string
     * @throws MissingPKException
     */
    public function sign(string $message): string
    {
        if (!$this->privateKey){
            throw new MissingPKException('Is necesary the private key');
        }
        if (!openssl_sign($message, $sign, $this->privateKey, $this->algorithm)) {
            throw new MissingPKException('The correct algorithm is required');
        }
        return base64_encode($sign);
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
        $success = openssl_verify($message, base64_decode($signatureInBase64), $this->publicKey, $this->algorithm);
        return ($success == 1);
    }
}
