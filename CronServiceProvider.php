<?php

namespace KDuma\Cron;

use Illuminate\Support\ServiceProvider;

/**
 * Class CronServiceProvider.
 */
class CronServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.queue.cron', function ($app) {
            return new CronCommand($app['queue.worker']);
        });

        $this->commands('command.queue.cron');
        $this->app->singleton('queue.worker', function ($app) {
            return new CronWorker($app['queue'], $app['queue.failer'], $app['events']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'queue.worker',
        ];
    }
}
