<?php

namespace Exitialis\Tests;

use Illuminate\Console;
use Exitialis\Mas\MasServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as Test;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase;

class MasTestCase extends TestCase
{

    protected function getPackageProviders($app)
    {
        return ['Exitialis\Mas\MasServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

}