<?php 

namespace Tests;

use Tests\Concerns\Reflectors;
use Unicodeveloper\Paystack\Paystack;
use Unicodeveloper\Paystack\PaystackServiceProvider;

/**
 * @coversDefaultClass \Unicodeveloper\Paystack\PaystackServiceProvider
 */
class ServiceProviderTest extends TestCase {

	/**
	 * Test that service provider returns instance of paystack.
	 * 
	 * @test
	 * @return Unicodeveloper\Paystack\Paystack
	 */
	function it_returns_instance_of_paystack () {

		$paystack = $this->app["laravel-paystack"];

		$this->assertInstanceOf("Unicodeveloper\Paystack\Paystack", $paystack);

		return $paystack;
	}

	/**
	 * Test that service provider returns proper alias.
	 *
	 * @test
	 * @covers Unicodeveloper\Paystack\PaystackServiceProvider::provides
	 * @return void
	 */
	function it_provides_alias () {

		$paystackServiceProvider = new PaystackServiceProvider($this->app);

		$alias = $paystackServiceProvider->provides();

		$this->assertTrue(in_array("laravel-paystack", $alias));

		return $paystackServiceProvider;
	}

	/**
	 * Tests that baseUrl is set when service provider is invoked.
	 * 
	 * @test
	 * @depends it_provides_alias
	 * @return void
	 */
	function it_sets_base_url (PaystackServiceProvider $paystackServiceProvider) {

		$reflection = new Reflectors($paystackServiceProvider);

		$reflection->invokeMethod("setBaseUrl");

		$baseUrl = $reflection->fetchProperty("baseUrl");

		$this->assertEquals($this->app["config"]->get("paystack.paymentUrl"), $baseUrl->value);
	}

	/**
	 * Tests that secretKey is set when service provider is invoked.
	 * 
	 * @test
	 * @depends it_provides_alias
	 * @return void
	 */
	function it_sets_secret_token (PaystackServiceProvider $paystackServiceProvider) {

		$reflection = new Reflectors($paystackServiceProvider);

		$reflection->invokeMethod("setSecretToken");

		$secretKey = $reflection->fetchProperty("secretKey");

		$this->assertEquals($this->app["config"]->get("paystack.secretKey"), $secretKey->value);
	}

	/**
	 * Tests that client is set when service provider is invoked.
	 * 
	 * @test
	 * @depends it_provides_alias
	 * @return void
	 */
	function it_sets_client (PaystackServiceProvider $paystackServiceProvider) {

		$reflection = new Reflectors($paystackServiceProvider);

		$reflection->invokeMethod("setClient");

		$client = $reflection->fetchProperty("client");

		$this->assertInstanceOf("GuzzleHttp\Client", $client->value);
	}

	/**
	 * Tests that setDependencies in called without errors.
	 * 
	 * @test
	 * @depends it_provides_alias
	 * @return void
	 */
	function it_calls_set_dependencies_without_errors (PaystackServiceProvider $paystackServiceProvider) {

		$reflection = new Reflectors($paystackServiceProvider);

		$reflection->invokeMethod("setDependencies");

		$this->assertTrue(true);
	}

	/**
	 * Tests bootstrapConfig.
	 * 
	 * @test
	 * @covers ::bootstrapConfig
	 * @depends it_provides_alias
	 * @return void
	 */
	function it_calls_bootstrap_config_without_errors (PaystackServiceProvider $paystackServiceProvider) {

		$reflection = new Reflectors($paystackServiceProvider);

		$reflection->invokeMethod("bootstrapConfig");

		$this->assertTrue(true);
	}
}
