<?php

namespace Exitialis\Mas;

use Exitialis\Mas\Repositories\KeyRepository;
use Exitialis\Mas\Repositories\UserRepository;
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

        $this->app->singleton(UserRepository::class, function() {
            return new UserRepository(User::class, config('mas.repositories.user'));
        });

        $this->app->singleton(KeyRepository::class, function() {
            return new KeyRepository(MasKey::class);
        });

    }
}
