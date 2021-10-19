<?php

namespace Tests;

use E4\Messaging\Providers\MessagingServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            MessagingServiceProvider::class,
        ];
    }
}
