<?php

namespace E4\Messaging\Utils\Encryption;

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

    public function decrypt(string $message): string
    {
        $ivDecrypt = substr(base64_decode($message), 0, $this->lengthIv);
        $messageDecrypt = substr(base64_decode($message), $this->lengthIv);
        return openssl_decrypt($messageDecrypt, $this->encryptMethod, $this->key, OPENSSL_RAW_DATA, $ivDecrypt);
    }
}
