<?php 

namespace Tests;

use Tests\Concerns\Reflectors;
use Unicodeveloper\Paystack\Paystack;
use Unicodeveloper\Paystack\PaystackServiceProvider;

class ServiceProviderTest extends TestCase {

	/**
	 * Test that service provider returns instance of paystack.
	 * 
	 * @test
	 * @return Unicodeveloper\Paystack\Paystack
	 */
	function it_returns_instance_of_paystack () {

		$paystack = $this->app->make("laravel-paystack");

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
}
