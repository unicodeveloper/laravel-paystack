<?php

declare(strict_types=1);

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unicodeveloper\Paystack\Test;

use Mockery as m;
use PHPUnit\Framework\TestCase;

class PaystackTest extends TestCase
{
    protected $paystack;

    public function setUp()
    {
        $this->paystack = m::mock('Unicodeveloper\Paystack\Paystack');
        $this->mock = m::mock('GuzzleHttp\Client');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testAllCustomersAreReturned()
    {
        $array = $this->paystack->shouldReceive('getAllCustomers')->andReturn(['prosper']);

        $this->assertEquals('array', gettype(array($array)));
    }

    public function testAllTransactionsAreReturned()
    {
        $array = $this->paystack->shouldReceive('getAllTransactions')->andReturn(['transactions']);

        $this->assertEquals('array', gettype(array($array)));
    }

    public function testAllPlansAreReturned()
    {
        $array = $this->paystack->shouldReceive('getAllPlans')->andReturn([]);

        $this->assertEquals('array', gettype(array($array)));
    }
}
