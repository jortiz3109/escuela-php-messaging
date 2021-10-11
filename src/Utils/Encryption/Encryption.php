<?php

namespace Messaging\Utils\Encryption;

/**
 * Clase que permite encriptar y desencriptar los mensajes
 * Estos se hacen por medio de encrypt_openssl
 * 
 */

class Encryption
{
    private $secret_key;
    private $secret_iv;
    private $encrypt_method;
    private $algorith;

    private $key;
    private $iv;

    public function __construct(string $secret_key, string $secret_iv, string $encrypt_method, string $algorith)
    {
        $this->secret_key = $secret_key;
        $this->secret_iv = $secret_iv;
        $this->encrypt_method = $encrypt_method;
        $this->algorith = $algorith;

        $this->configure_keys();
    }

    /**
     * Función para configurar las claves del openssl 
     */
    public function configure_keys(): void
    {
        $this->key = hash($this->algorith, $this->secret_key);
        $length_iv = openssl_cipher_iv_length($this->encrypt_method);
        $this->iv = substr(hash($this->algorith, $this->secret_iv), 0, $length_iv);
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
        $output = base64_encode(openssl_encrypt($message, $this->encrypt_method, $this->key, 0, $this->iv));

        return $output;
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
        $output = openssl_decrypt(base64_decode($message), $this->encrypt_method, $this->key, 0, $this->iv);

        return $output;
    }
}
