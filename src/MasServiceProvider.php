<?php

namespace Exitialis\Mas;

use Illuminate\Support\ServiceProvider;

class MasServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([__DIR__ . '/../config/mas.php' => config_path('mas.php')], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => $this->app->databasePath() . '/migrations'
        ], 'migrations');

        $routes = __DIR__ . "/Http/routes.php";
        if (file_exists($routes))
            require_once $routes;
        else
            throw new \Exception("Mas routes not found");
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mas.php', 'mas');

        $this->app->bind("Mas", function($app){
           return new Mas;
        });
    }
}
