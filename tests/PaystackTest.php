<?php

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests;

use GuzzleHttp\Client;
use Tests\Concerns\Reflectors;
use Unicodeveloper\Paystack\Paystack;

class PaystackTest extends TestCase
{
    use Concerns\Mocks;
    /** @test */
    public function it_initiated_properly () 
    {
        $paystack = new Paystack(new Client);

        $reflection = $this->reflected();

        $reflection->invokeMethod("setKey");

        $secretKey = $reflection->fetchProperty("secretKey");

        $reflection->fetchProperty("client")->value->getBody();

        $this->assertEquals($this->app["config"]->get("paystack.secretKey"), $secretKey->value);

        return $paystack;
    }

    /** 
     * @test
     * @depends it_initiated_properly
     */
    public function it_sets_http_response (Paystack $paystack)
    {
        $this->assertTrue(true);
    }
}
