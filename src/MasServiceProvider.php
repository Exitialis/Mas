<?php

namespace Exitialis\Mas;

use Exitialis\Mas\Managers\AuthManager;
use Exitialis\Mas\Managers\KeyManager;
use Exitialis\Mas\Repository\Contracts\KeyRepositoryInterface;
use Exitialis\Mas\Repository\Contracts\RepositoryInterface;
use Exitialis\Mas\Repository\Contracts\UserRepositoryInterface;
use Exitialis\Mas\Repository\Eloquent\KeyRepository;
use Exitialis\Mas\Repository\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class MasServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @throws \Exception
     */
    public function boot()
    {

        $this->publishes([__DIR__ . '/../config/mas.php' => config_path('mas.php')], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => $this->app->databasePath() . '/migrations'
        ], 'migrations');

        $routes = __DIR__ . "/Http/routes.php";
        $helpers = __DIR__ . "/helpers.php";
        if (file_exists($routes))
            require_once $routes;
        else
            throw new \Exception("Mas routes not found");

        if (file_exists($helpers))
            require_once $helpers;
        else
            throw new \Exception("Mas helpers not found");

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mas.php', 'mas');

        $this->app->singleton(UserRepositoryInterface::class, function($app) {
            return new UserRepository($app, config('mas.repositories.user'));
        });

        $this->app->singleton(KeyRepositoryInterface::class, function($app) {
            return new KeyRepository($app);
        });

        $this->app->bind(KeyManager::class, function($app) {
            return new KeyManager($app[KeyRepositoryInterface::class]);
        });

        $this->app->bind(AuthManager::class, function ($app) {
            return new AuthManager($app[UserRepositoryInterface::class], $app[KeyManager::class]);
        });

    }
}
