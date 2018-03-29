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

use GuzzleHttp\Client;
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
     * Placeholder for base URL.
     * 
     * @var string
     */
    protected $baseUrl;

    /**
     * Placeholder for secret key.
     * 
     * @var string
     */
    protected $secretKey;

    /**
     * Placeholder for GuzzleHttp\Client.
     * 
     * @var \GuzzleHttp\Cient
     */
    protected $client;

    /**
    * Publishes all the config file this package needs to function
    */
    public function boot()
    {
        $config = realpath(__DIR__.'/../resources/config/paystack.php');

        $this->publishes([
            $config => config_path('paystack.php')
        ]);
    }

    /**
    * Register the application services.
    */
    public function register()
    {
        $this->bootstrapConfig();

        $this->app->singleton('laravel-paystack', function () {

            return new Paystack($this->client);

        });
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
     * Bootstraps configuration if configuration file exists.
     * 
     * @return void
     */
    protected function bootstrapConfig() 
    {    
        $this->setDependencies();
        $this->setClient();
    }

    /**
     * Called upon to set required meta dependencies.
     */
    protected function setDependencies()
    {
        $this->setBaseUrl();

        $this->setSecretToken();
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
        $this->secretKey = $this->app["config"]->get("paystack.secretKey");
    }

    /**
     * Set guzzle client.
     */
    protected function setClient()
    {
        $this->client = new Client([
            "base_uri" => $this->baseUrl,
            'headers' => [
                'Authorization' => "Bearer {$this->secretKey}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json'
            ],
        ]);
    }
}
