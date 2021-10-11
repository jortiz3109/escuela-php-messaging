<?php

namespace E4\Messaging\Utils\Encryption;

/**
 * Clase que permite encriptar y desencriptar los mensajes
 * Estos se hacen por medio de encrypt_openssl
 * 
 */

class Encryption
{
    private $secretKey;
    private $secretIv;
    private $encryptMethod;
    private $algorith;

    private $key;
    private $iv;

    public function __construct(string $secretKey, string $secretIv, string $encryptMethod, string $algorith)
    {
        $this->secretKey = $secretKey;
        $this->secretIv = $secretIv;
        $this->encryptMethod = $encryptMethod;
        $this->algorith = $algorith;

        $this->configureKeys();
    }

    /**
     * Función para configurar las claves del openssl 
     */
    public function configureKeys(): void
    {
        $this->key = hash($this->algorith, $this->secretKey);
        $lengthIv = openssl_cipher_iv_length($this->encryptMethod);
        $this->iv = substr(hash($this->algorith, $this->secretIv), 0, $lengthIv);
    }

    /**
     * Metodo para encriptar
     * 
     * @param string $message
     * 
     * @return string
     */
    public function encrypt(string $message): string
    {
        return base64_encode(openssl_encrypt($message, $this->encryptMethod, $this->key, 0, $this->iv));
    }

   /**
     * Metodo para desencriptar
     * 
     * @param string $message
     * 
     * @return string
     */
    public function decrypt(string $message): string
    {
        return openssl_decrypt(base64_decode($message), $this->encryptMethod, $this->key, 0, $this->iv);
    }
}
