<?php
namespace Tests;

use Mockery;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

    public $m;

    protected $envVars;

    function setUp () {

        $this->m = new Mockery;

        parent::setUp();
    }

    /**
     * Clear mockery after every test in preparation for a new mock.
     *
     * @return void
     */
    function tearDown() {

        $this->m->close();

        parent::tearDown();
    }

    /**
     * Register package.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array      Packages to register
     */
    protected function getPackageProviders($app)
    {
        return [ "Unicodeveloper\Paystack\PaystackServiceProvider" ];
    }

    /**
     * Get alias packages from app.
     *
     * @param  \illuminate\Foundation\Application $app
     * @return array      Aliases.
     */
    protected function getPackageAliases($app)
    {
        return [
            "Paystack" => "Unicodeveloper\Paystack\Facades\Paystack"
        ];
    }

    /**
     * Configure Environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->envVars = require __DIR__. "/Stubs/config.php";

        array_walk($this->envVars, function($value, $key) use ($app) {
            $app["config"]->set("paystack.{$key}", $value);
        });
    }
}