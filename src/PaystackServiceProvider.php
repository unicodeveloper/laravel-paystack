<?php

declare(strict_types=1);

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unicodeveloper\Paystack;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application as LaravelApp;
use Illuminate\Support\ServiceProvider;
use Xeviant\Paystack\Client;
use Laravel\Lumen\Application as LumenApp;

class PaystackServiceProvider extends ServiceProvider
{

    /*
    * Indicates if loading of the provider is deferred.
    *
    * @var bool
    */
    protected $defer = false;

    /**
    * Publishes all the config file this package needs to function
    */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Sets up Paystack configuration file
     */
    protected function setupConfig()
    {
        $config = realpath($raw = __DIR__.'/../resources/config/paystack.php') ?: $raw;

        if ($this->app instanceof LaravelApp && $this->app->runningInConsole()) {
            $this->publishes([
                $config => config_path('paystack.php')
            ]);
        } elseif ($this->app instanceof LumenApp) {
            $this->app->configure('paystack');
        }

        $this->mergeConfigFrom($config, 'paystack');
    }

    /**
    * Register the application services.
    */
    public function register()
    {
        $this->app->bind('laravel-paystack', function () {
            return new Paystack;
        });

        $this->registerPaystackFactory()
            ->registerPaystackManager()
            ->registerCoreBindings();
    }

    /**
     * Registers the Paystack factory
     *
     * @return $this
     */
    protected function registerPaystackFactory()
    {
        $this->app->singleton('paystack.factory', function (Container $container) {
            $cache = $container['cache'];

            return new PaystackFactory($cache);
        });

        $this->app->alias('paystack.factory', PaystackFactory::class);

        return $this;
    }

    /**
     * Registers Paystack manager
     *
     * @return $this
     */
    protected function registerPaystackManager()
    {
        $this->app->singleton('paystack', function (Container $container) {
            $config = $container['config'];
            $factory = $container['paystack.factory'];

            return new PaystackManager($config, $factory);
        });

        $this->app->alias('paystack', PaystackManager::class);

        return $this;
    }

    /**
     * Registers the Core Paystack Binding
     *
     * @return $this
     */
    protected function registerCoreBindings()
    {
        $this->app->bind('paystack.connection', function (Container $container) {
           $manager = $container['paystack'];

           return $manager->connection();
        });

        $this->app->alias('paystack.connection', Client::class);

        return $this;
    }

    /**
    * Get the services provided by the provider
    * @return array
    */
    public function provides()
    {
        return [
            'paystack',
            'paystack.factory',
            'laravel-paystack',
            'paystack.connection',
        ];
    }
}
