<?php

namespace E4\Pigeon\Utils\Encryption;

use E4\Pigeon\Exceptions\DecryptMethodException;

class Encryption
{
    private string $secretKey;
    private string $encryptMethod;
    private string $algorithm;

    private string $key;
    private string $iv;
    private int $lengthIv;

    public function __construct(string $secretKey, string $encryptMethod, string $algorithm)
    {
        $this->secretKey = $secretKey;
        $this->encryptMethod = $encryptMethod;
        $this->algorithm = $algorithm;

        $this->configureKeys();
    }

    private function configureKeys(): void
    {
        $this->key = hash($this->algorithm, $this->secretKey);
        $this->lengthIv = openssl_cipher_iv_length($this->encryptMethod);
        $this->iv = openssl_random_pseudo_bytes($this->lengthIv);
    }

    public function encrypt(string $message): string
    {
        return base64_encode($this->iv . openssl_encrypt($message, $this->encryptMethod, $this->key, OPENSSL_RAW_DATA, $this->iv));
    }

    /**
     * @param string $message
     * @return string
     * @throws DecryptMethodException
     */
    public function decrypt(string $message): string
    {
        $ivDecrypt = substr(base64_decode($message), 0, $this->lengthIv);
        $messageEncrypt = substr(base64_decode($message), $this->lengthIv);
        try {
            $messageDecrypted = openssl_decrypt($messageEncrypt, $this->encryptMethod, $this->key, OPENSSL_RAW_DATA, $ivDecrypt);
        } catch (\Exception $e) {
            throw new DecryptMethodException('Error with openssl_decrypt ' . $e);
        }
        if (!$messageDecrypted) {
            throw new DecryptMethodException('There was an error in the decrypt method');
        }
        return $messageDecrypted;
    }
}
