<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default App Connection Name
    |--------------------------------------------------------------------------
    */
    'default' => env('MSAPP_CONNECTION', 'rabbitmq'),

    /*
    |--------------------------------------------------------------------------
    | Messaging Apps Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [
        'rabbitmq' => [
            'driver' => 'rabbitmq',
            'ssl' => env('MSAPP_RABBITMQ_SSL', false),
            'host' => [
                'host' => env('MSAPP_RABBITMQ_HOST', '127.0.0.1'),
                'port' => env('MSAPP_RABBITMQ_PORT', '5672'),
                'username' => env('MSAPP_RABBITMQ_USERNAME', 'guest'),
                'password' => env('MSAPP_RABBITMQ_PASSWORD', 'guest'),
                'vhost' => env('MSAPP_RABBITMQ_VHOST', '/'),
            ],
            'exchange' => [
                'name' => env('MSAPP_RABBITMQ_EXCHANGE_NAME', 'direct_exchange'),
                'type' => env('MSAPP_RABBITMQ_EXCHANGE_TYPE', 'direct'),
            ],
            'queue' => [
                'name' => env('MSAPP_RABBITMQ_QUEUE_NAME', 'queue'),
            ],
            'ssl_options' => [
                'cafile' => env('MSAPP_RABBITMQ_SSL_CAFILE', null),
                'local_cert' => env('MSAPP_RABBITMQ_SSL_LOCAL_CERT', null),
                'local_key' => env('MSAPP_RABBITMQ_SSL_LOCAL_KEY', null),
                'verify_peer' => env('MSAPP_RABBITMQ_SSL_VERIFY_PEER', true),
                'passphrase' => env('MSAPP_RABBITMQ_SSL_PASSPHRASE', null),
            ],
            //'backupConnection' => 'rabbitmqBackup',
        ],
        /*'rabbitmqBackup' => [
            'driver' => 'rabbitmq',
            ...
        ]*/
    ],
    'signature' => [
        'algorithm' => env('MSAPP_SIGNATURE_ALGORITHM', OPENSSL_ALGO_SHA256),
        'publicKey' => env('MSAPP_SIGNATURE_PUBLICKEY_PATH'),
        'privateKey' => env('MSAPP_SIGNATURE_PRIVATEKEY_PATH', null),
    ],
    'encryption' => [
        'secretKey' => env('MSAPP_ENCRYPT_SECRETKEY', 'CLASS-MESSAGE-KEY'),
        'method' => env('MSAPP_ENCRYPT_METHOD', 'AES-256-CBC'),
        'algorithm' => env('MSAPP_ENCRYPT_ALGORITHM', 'sha256'),
    ],
];
