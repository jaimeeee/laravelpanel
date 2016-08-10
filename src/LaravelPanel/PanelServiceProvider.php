<?php

namespace Jaimeeee\Panel;

use View;

use Illuminate\Support\ServiceProvider;
use Jaimeeee\Panel\Entity;

class PanelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set config file
        $this->publishes([
            __DIR__ . '/config/panel.php' => config_path('panel.php'),
        ]);
        
        // Load routes
        if (!$this->app->routesAreCached())
        {
            require __DIR__ . '/http/routes.php';
        }
        
        // Load views
        $this->loadViewsFrom(__DIR__ . '/views', 'panel');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configuration file
        $this->mergeConfigFrom(
            __DIR__ . '/config/panel.php', 'panel'
        );
        
        // Pass links to the sidebar
        View::composer('panel::layout', function($view) {
            View::share('list', Entity::entityList());
        });
    }
}
