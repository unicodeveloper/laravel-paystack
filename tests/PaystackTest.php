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
        $paystack = $reflection = $this->reflected();

        $reflection->invokeMethod("setKey");

        $secretKey = $reflection->fetchProperty("secretKey");

        $this->assertEquals($this->app["config"]->get("paystack.secretKey"), $secretKey->value);

        return $paystack;
    }

    /** 
     * @test
     * @depends it_initiated_properly
     * @expectedException Unicodeveloper\Paystack\Exceptions\isNullException
     */
    public function it_sets_http_response_exception (Reflectors $paystack)
    {
        $paystack->invokeMethod("setHttpResponse", ["/", null]);
    }

    /** 
     * @test
     * @depends it_initiated_properly
     */
    public function it_sets_http_response (Reflectors $paystack)
    {
        $paystack->invokeMethod("setHttpResponse", ["/", "POST"]);

        $response = $paystack->fetchProperty("response");

        $this->assertInstanceOf("GuzzleHttp\Psr7\Response", $response->value);
    }

    /** 
     * @test
     * @doesNotPerformAssertions
     */
    public function it_makes_payment_request ()
    {
        $reflection = $this->reflected();

        $reflection->invokeMethod("makePaymentRequest");
    }

    /** @test */
    public function it_gets_authorization_url ()
    {
        $reflection = $this->reflected();

        $reflection->invokeMethod("getAuthorizationUrl", []);

        $authorizationUrl = $reflection->fetchProperty("authorizationUrl");

        $body = $this->getResourse();

        $this->assertEquals($body["payment_response"]["data"]["authorization_url"], $authorizationUrl->value);
    }

    /** @test */
    public function it_gets_authorization_response ()
    {
        $reflection = $this->reflected();

        $response = $reflection->invokeMethod("getAuthorizationResponse");

        $this->assertInternalType("array", $response);
    }

    /** @test */
    public function it_gets_response ()
    {
        $reflection = $this->reflected();

        $reflection->setProperty("response", $this->response("payment_response"));

        $response = $reflection->invokeMethod("getResponse");

        $this->assertInternalType("array", $response);
    }

    /** @test */
    public function it_verifies_transaction_at_gateway ()
    {
        $reflection = $this->reflected("validation_response_success");

        $reflection->invokeMethod("verifyTransactionAtGateway");

        $response = $reflection->fetchProperty("response");

        $this->assertInstanceOf("GuzzleHttp\Psr7\Response", $response->value);
    }

    /** @test */
    public function it_verifies_transaction_validity_success ()
    {
        $reflection = $this->reflected("validation_response_success");

        $valid = $reflection->invokeMethod("isTransactionVerificationValid");

        $this->assertTrue($valid);
    }

    /** @test */
    public function it_verifies_transaction_validity_false ()
    {
        $reflection = $this->reflected("validation_response_invalid");

        $valid = $reflection->invokeMethod("isTransactionVerificationValid");

        $this->assertFalse($valid);
    }

    /** @test */
    public function it_verifies_transaction_validity_other ()
    {
        $reflection = $this->reflected("validation_response_other");

        $valid = $reflection->invokeMethod("isTransactionVerificationValid");

        $this->assertFalse($valid);
    }
}
