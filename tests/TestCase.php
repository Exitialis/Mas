<?php

namespace Exitialis\Mas\Tests;

use Exitialis\Mas\MasServiceProvider;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{

    /**
     * Генератор значений.
     *
     * @var Generator
     */
    protected $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->app = $this->createApplication();

        $this->setEnvironment();

        $this->loadFactories(__DIR__ . '/../database/factories/');
    }

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app->register(MasServiceProvider::class);

        return $app;
    }

    /**
     * run package database migrations
     *
     * @return void
     */
    public function migrate()
    {
        $fileSystem = new Filesystem;

        foreach ($fileSystem->files(__DIR__ . "/../database/migrations/") as $file) {
            $fileSystem->requireOnce($file);
            $className = $this->getMigrateClass($file);

            (new $className)->up();
        }
    }

    /**
     * Получить класс для файла миграции.
     *
     * @param $fileName
     * @return mixed
     */
    protected function getMigrateClass($fileName)
    {
        $name = str_replace('.php', '', $fileName);

        //Разбиваем по _
        $name = explode('_', $name);

        //Вырезаем дату создания
        $name = array_slice($name, 4);

        //Склееваем обратно в строку и возвращаем в CamelCase
        return str_replace(' ', '', ucwords(join(' ', $name)));
    }

    /**
     * Загрузить фактори классы.
     *
     * @param $path
     */
    protected function loadFactories($path)
    {
        $this->app->make(\Illuminate\Database\Eloquent\Factory::class)->load($path);
    }


    /**
     * Настроить конфиги под тестовое окружение.
     */
    protected function setEnvironment()
    {
        //dd($this->app);
        // Setup default database to use sqlite :memory:
        $this->app['config']->set('database.default', 'test');
        $this->app['config']->set('database.connections.test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);


    }


}