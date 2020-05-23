<?php

namespace Unicodeveloper\Paystack\Test;

use PHPUnit_Framework_TestCase;

class HelpersTest extends PHPUnit_Framework_TestCase {

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