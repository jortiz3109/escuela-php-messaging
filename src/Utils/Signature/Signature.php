<?php

namespace E4\Messaging\Utils\Signature;

use E4\Messaging\Utils\Signature\Exceptions\SignatureException;
use Exception;

class Signature
{
    private ?string $privateKey;
    private string $publicKey;
    private int $algorithm;

    public function __construct(int $algorithm, string $publicKey, string|null $privateKey = null)
    {
        $this->algorithm = $algorithm;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * @param string $message
     *
     * @return string
     * @throws SignatureException
     * @throws Exception
     */
    public function sign(string $message): string
    {
        if (!$this->privateKey) {
            throw new SignatureException('Is necessary the private key');
        }
        if (!openssl_sign($message, $signature, $this->privateKey, $this->algorithm)) {
            throw new SignatureException('The correct algorithm is required');
        }
        return base64_encode($signature);
    }

    /**
     * @param string $message
     * @param string $signatureInBase64
     *
     * @return bool
     * @throws SignatureException
     * @throws Exception if the public key is wrong
     */
    public function verify(string $message, string $signatureInBase64): bool
    {
        $verified = openssl_verify($message, base64_decode($signatureInBase64), $this->publicKey, $this->algorithm);
        if ($verified == -1) {
            throw new SignatureException('The signature format or the signature algorithm is wrong');
        }
        return $verified == 1;
    }
}
