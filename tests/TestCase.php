<?php

namespace Exitialis\Mas\Tests;

use Exitialis\Mas\User;
use Faker\Factory;
use Illuminate\Console;
use Exitialis\Mas\MasServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase as Test;

class TestCase extends Test
{

    /**
     * Генератор значений.
     *
     * @var Faker\Generator
     */
    protected $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    /**
     * Подключить service provider пакета.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [MasServiceProvider::class];
    }

    /**
     * Настроить конфиги под тестовое окружение.
     *
     * @param \Illuminate\Foundation\Application $app
     */
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