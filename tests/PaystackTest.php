<?php

namespace Unicodeveloper\Paystack\Test;

use PHPUnit_Framework_TestCase;
use Unicodeveloper\Paystack\Paystack;

class PaystackTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that true does in fact equal true
     */
    public function testTrueIsTrue()
    {
        $this->assertTrue(true);
    }

    public function testTranxRef()
    {
        $paystack =  new Paystack();
        echo $paystack->genTranxRef();
    }
}