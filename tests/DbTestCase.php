<?php

namespace Exitialis\Mas\Tests;

use Exitialis\Mas\User;
use Illuminate\Console;
use Exitialis\Mas\MasServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as Test;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;


class DbTestCase extends TestCase
{
    /**
     * Тестовый пользователь.
     *
     * @var User
     */
    protected $user;

    /**
     * Настройки для тестирования.
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom([
            '--database' => 'test',
            '--realpath' => realpath(__DIR__.'/../database/migrations'),
        ]);

        $this->withFactories(__DIR__.'/../database/factories');

        $this->setupUsers();
    }

    /**
     * Создание в тестовой базе таблицы с пользователями и полями из конфига.
     */
    protected function setupUsers()
    {
        $config = config('mas.repositories.user');

        Schema::create($config['table_name'], function (Blueprint $table) use ($config) {
            $table->increments($config['key']);
            $table->timestamps();

            $table->string($config['login_column']);
            $table->string($config['password_column']);
        });

        $this->user = factory(User::class)->create();
    }

    

}