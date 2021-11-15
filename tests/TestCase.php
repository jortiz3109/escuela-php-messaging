<?php

namespace Tests;

use E4\Pigeon\Providers\PigeonServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PigeonServiceProvider::class,
        ];
    }
}
