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
            __DIR__ . '/config/panel/User.yml' => config_path('panel/User.yml'),
        ], 'config');
        
        // Set the stylesheets around
        $this->publishes([
            __DIR__ . '/assets/css/panel.css' => public_path('css/panel.css'),
            __DIR__ . '/resources/sass/_normalize.scss' => resource_path('assets/sass/_normalize.scss'),
            __DIR__ . '/resources/sass/panel.scss' => resource_path('assets/sass/panel.scss'),
        ], 'public');
        
        // Load routes
        if (!$this->app->routesAreCached()) {
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
            View::share('list', Entity::all());
        });
    }
}
