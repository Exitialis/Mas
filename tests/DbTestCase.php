<?php

namespace Exitialis\Tests;

use Illuminate\Console;
use Exitialis\Mas\MasServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as Test;

class DbTestCase extends Test
{

    /**
     * Setup DB before tests.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        /*$this->app['config']->set('database.default','sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');*/

        //$this->migrate();
    }

    /**
     * @return mixed
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->register(MasServiceProvider::class);

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

}