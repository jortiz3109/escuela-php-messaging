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

    public function __construct(string $secret_key, string $secret_iv, string $encrypt_method, string $algorith)
    {
        $this->secret_key = $secret_key;
        $this->secret_iv = $secret_iv;
        $this->encrypt_method = $encrypt_method;
        $this->algorith = $algorith;
    }

    /**
     * Metodo para encriptar
     * 
     * @param string $mesasge
     * 
     * @return string
     */
    public function encrypt_openssl(string $mesasge): string
    {
        // hash
        $key = hash('sha256', $this->secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $this->secret_iv), 0, 16);

        $output = base64_encode(openssl_encrypt($mesasge, $this->encrypt_method, $key, 0, $iv));

        return $output;
    }

   /**
     * Metodo para desencriptar
     * 
     * @param string $mesasge
     * 
     * @return string
     */
    public function decrypt_openssl(string $message): string
    {
        // hash
        $key = hash('sha256', $this->secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $this->secret_iv), 0, 16);
        
        $output = openssl_decrypt(base64_decode($message), $this->encrypt_method, $key, 0, $iv);

        return $output;
    }
}