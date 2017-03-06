<?php

namespace Exitialis\Mas\Tests;

use Exitialis\Mas\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;


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

        $this->migrate();

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