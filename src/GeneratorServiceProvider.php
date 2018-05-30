<?php

namespace Kodeloper\Generator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->aliasMiddleware('generator', \Kodeloper\Generator\Middleware\GeneratorMiddleware::class);

        $this->publishes([
            __DIR__.'/Config/generator.php' => config_path('generator.php'),
        ], 'generator_config');

        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        $this->loadTranslationsFrom(__DIR__ . '/Translations', 'generator');

        $this->publishes([
            __DIR__ . '/Translations' => resource_path('lang/vendor/generator'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/Views', 'generator');

        $this->publishes([
            __DIR__ . '/Views' => resource_path('views/vendor/generator'),
        ]);

        $this->publishes([
            __DIR__ . '/Assets' => public_path('vendor/generator'),
        ], 'generator_assets');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Kodeloper\Generator\Commands\GeneratorCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/generator.php', 'generator'
        );
    }
}
