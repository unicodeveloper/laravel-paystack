<?php 

namespace Unicodeveloper\Paystack\Test;

class HelpersTest extends TestCase {

	/**
	 * Tests that helper returns
	 * 
	 * @test
	 * @return void
	 */
	function it_returns_instance_of_paystack () {

		$this->assertInstanceOf("Unicodeveloper\Paystack\Paystack", paystack());
	}
}