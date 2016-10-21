<?php

namespace KDuma\Cron;

use Illuminate\Support\ServiceProvider;

/**
 * Class WebCronServiceProvider.
 */
class WebCronServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('webcron.php'),
        ]);

        if (! $this->app->routesAreCached()) {
            app('router')->get('cron/{secret?}', [
                'as' => 'KDuma.cron',
                'uses' => WebCronController::class.'@webCron',
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
            __DIR__.'/config.php', 'webcron'
        );
    }
}
