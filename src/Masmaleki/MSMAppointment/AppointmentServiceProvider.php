<?php

namespace Masmaleki\MSMAppointment;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppointmentServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish Configuration File to base Path.
        $this->publishes([
            __DIR__ . '/config/MSMAppointment.php' => base_path('config/MSMAppointment.php'),
            __DIR__ . '/migrations' => $this->app->databasePath() . '/migrations',
            __DIR__ . '/resources/views' => base_path('/resources/views/vendor/msm-appointments')
        ]);
        $this->loadViewsFrom(__DIR__.'/resources/views', 'msm-appointments');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFactory($this->app);
        $this->registerManager($this->app);
        $this->registerRoutes($this->app);
    }

    /**
     * Register the factory class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function registerFactory(Application $app)
    {
        $app->singleton('MSMAppointment.factory', function () {
            return new Factories\MSMAppointmentFactory();
        });

        $app->alias('MSMAppointment.factory', 'Masmaleki\MSMAppointment\Factories\MSMAppointmentFactory');
    }


    /**
     * Register the manager class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function registerManager(Application $app)
    {
        $app->singleton('MSMAppointment', function ($app) {
            $config  = $app['config'];
            $factory = $app['MSMAppointment.factory'];

            return new MSMAppointment($config, $factory);
        });

        $app->alias('MSMAppointment', 'Masmaleki\MSMAppointment\MSMAppointment');
    }

    /**
     * Get the routes services provided by the provider.
     *
     * @return routes
     */
    protected function registerRoutes(Application $app)
    {
        $app['router']->group(['namespace' => 'Masmaleki\MSMAppointment\Http\Controllers', "prefix" => "msm"], function () {
            require __DIR__ . '/Http/routes.php';
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
            'MSMAppointment',
            'MSMAppointment.factory',
        ];
    }
}
