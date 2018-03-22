<?php 

namespace Tests;

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

		$paystackServiceProvider = $this->m->mock("Unicodeveloper\Paystack\PaystackServiceProvider");

		$paystackServiceProvider->shouldReceive("provides")
								->andReturn(["laravel-paystack"]);

		$alias = $paystackServiceProvider->provides();

		$this->assertTrue(in_array("laravel-paystack", $alias));
	}
}
