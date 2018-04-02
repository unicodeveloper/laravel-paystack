<?php 

namespace Tests;

use Unicodeveloper\Paystack\Facades\Paystack;
use Tests\Concerns\Reflectors;

class FacadesTest extends TestCase {


	/**
	 * Test that facade returns instance of paystack.
	 * 
	 * @test
	 * @return void
	 */
	function facade_returns_paystack () {

		$paystack = new Paystack;

		$reflection = new Reflectors($paystack);


		$facadeAccessor = $reflection->invokeMethod("getFacadeAccessor");

		$this->assertEquals("laravel-paystack", $facadeAccessor);
	}
}