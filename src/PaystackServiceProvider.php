<?php

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unicodeveloper\Paystack;

// use Illuminate\Support\Facades\Config
use Illuminate\Support\ServiceProvider;

class PaystackServiceProvider extends ServiceProvider
{

    /*
    * Indicates if loading of the provider is deferred.
    *
    * @var bool
    */
    protected $defer = false;

    /**
     * Sets base url for paystack.
     * 
     * @var string
     */
    protected $baseUrl;

    /**
    * Publishes all the config file this package needs to function
    */
    public function boot()
    {
        $config = realpath(__DIR__.'/../resources/config/paystack.php');

        $this->publishes([
            $config => config_path('paystack.php')
        ]);

        $this->setBaseUrl();

        $this->setSecretToken();

        $this->setClient();

        $this->app->singleton('laravel-paystack', function () {

            return new Paystack(new Client);

        });
    }

    /**
    * Register the application services.
    */
    public function register()
    {
    }

    /**
    * Get the services provided by the provider
    * @return array
    */
    public function provides()
    {
        return ['laravel-paystack'];
    }

    /**
     * Set the base url from conig.
     */
    protected function setBaseUrl()
    {
        $this->baseUrl = $this->app["config"]->get("paystack.paymentUrl");
    }

    /**
     * Set the base url from conig.
     */
    protected function setSecretToken()
    {
        $this->baseUrl = $this->app["config"]->get("paystack.paymentUrl");
    }

    protected function setClient()
    {
        //
    }
}
