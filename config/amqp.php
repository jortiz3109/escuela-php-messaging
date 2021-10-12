<?php

return [

    'ssl' => env('AMQP_SSL', false),

    'host' => [
        'host' => env('AMQP_HOST', '127.0.0.1'),
        'port' => env('AMQP_PORT', '5672'),
        'username' => env('AMQP_USERNAME', 'guest'),
        'password' => env('AMQP_PASSWORD', 'guest'),
        'vhost' => env('AMQP_VHOST', '/'),
    ],

    'exchange' => [
        'name' => env('AMQP_EXCHANGE_NAME', 'direct_exchange'),
        'type' => env('AMQP_EXCHANGE_TYPE', 'direct')
    ],

    'queue' => [
        'name' => env('AMQP_QUEUE_NAME', 'queue')
    ],

    'ssl_options' => [
        'cafile' => env('AMQP_SSL_CAFILE', null),
        'local_cert' => env('AMQP_SSL_LOCAL_CERT', null),
        'local_key' => env('AMQP_SSL_LOCAL_KEY', null),
        'verify_peer' => env('AMQP_SSL_VERIFY_PEER', true),
        'passphrase' => env('AMQP_SSL_PASSPHRASE', null),
    ],

];