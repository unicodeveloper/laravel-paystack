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

    // Methods to test.
    const SET_KEY = "setKey";
    const SET_HTTP_RESPONSE = "setHttpResponse";
    const MAKE_PAYMENT_REQUEST = "makePaymentRequest";
    const GET_AUTHORIZATION_URL = "getAuthorizationUrl";
    const GET_ALL_CUSTOMERS = "getAllCustomers";
    const GET_ALL_PLANS = "getAllPlans";
    const GET_ALL_TRANSACTIONS = "getAllTransactions";
    const CREATE_PlAN = "createPlan";
    const FETCH_PLAN = "fetchPlan";
    const UPDATE_PLAN = "updatePlan";
    const CREATE_CUSTOMER = "createCustomer";
    const FETCH_CUSTOMER = "fetchCustomer";
    const UPDATE_CUSTOMER = "updateCustomer";

    // Properties
    const AUTHORIZATION_URL = "authorizationUrl";

    /** @test */
    public function it_initiated_properly () 
    {
        $paystack = $reflection = $this->reflected();

        $reflection->invokeMethod(self::SET_KEY);

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
        $paystack->invokeMethod(self::SET_HTTP_RESPONSE, ["/", null]);
    }

    /** 
     * @test
     * @depends it_initiated_properly
     */
    public function it_sets_http_response (Reflectors $paystack)
    {
        $paystack->invokeMethod(self::SET_HTTP_RESPONSE, ["/", "POST"]);

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

        $reflection->invokeMethod(self::MAKE_PAYMENT_REQUEST);
    }

    /** @test */
    public function it_gets_authorization_url ()
    {
        $reflection = $this->reflected();

        $reflection->invokeMethod(self::GET_AUTHORIZATION_URL, []);

        ${self::AUTHORIZATION_URL} = $reflection->fetchProperty(self::AUTHORIZATION_URL);

        $resource = $this->getResource();

        $this->assertEquals(
            $resource["payment_response"]["data"]["authorization_url"],
            ${self::AUTHORIZATION_URL}->value
        );
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

     /** @test */
    public function it_gets_payment_data ()
    {
        $resource = $this->getResource();

        $expected = json_encode($resource["validation_response_success"]);

        $reflection = $this->reflected("validation_response_success");

        $data = $reflection->invokeMethod("getPaymentData");

        $actual = json_encode(($data));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @expectedException Unicodeveloper\Paystack\Exceptions\PaymentVerificationFailedException
     */
    public function it_gets_payment_data_invalid_trans ()
    {
        $reflection = $this->reflected("validation_response_invalid");

        $reflection->invokeMethod("getPaymentData");
    }

    /** 
     * @test
     * @return Tests\Concerns\Reflectors $reflection
     */
    public function it_redirects_now ()
    {
        $reflection = $this->reflected();

        $redirectNow = $reflection->invokeMethod("redirectNow");

        $this->assertInstanceOf("Illuminate\Routing\Redirector", $redirectNow);

        return $reflection;
    }

    /** 
     * @test
     * @depends it_redirects_now
     * @param  Tests\Concerns\Reflectors $reflection
     */
    public function it_gets_access_code (Reflectors $reflection) 
    {
        $reflection->setProperty("response", $this->response("payment_response"));

        $code = $reflection->invokeMethod("getAccessCode");

        $expected = $this->getResource()["payment_response"]["data"]["access_code"];

        $this->assertEquals($expected, $code);
    }

    /** 
     * @test
     * @depends it_redirects_now
     */
    public function it_gens_trans_ref (Reflectors $reflection)
    {
        $ref = $reflection->invokeMethod("genTranxRef");

        $this->assertCount(25, str_split($ref));
    }

    /** @test */
    public function it_gets_all_customers()
    {
        $reflection = $this->reflected("all_customers");

        $actual = $reflection->invokeMethod(self::GET_ALL_CUSTOMERS);

        $expected = $this->getResource()["all_customers"]["data"];

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** @test */
    public function it_gets_all_plans ()
    {
        $reflection = $this->reflected("all_plans");

        $actual = $reflection->invokeMethod(self::GET_ALL_PLANS);

        $expected = $this->getResource()["all_plans"]["data"];

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** @test */
    public function it_gets_all_transactions () 
    {
        $reflection = $this->reflected("all_transactions");

        $actual = $reflection->invokeMethod(self::GET_ALL_TRANSACTIONS);

        $expected = $this->getResource()["all_transactions"]["data"];

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** 
     * @test
     * @doesNotPerformAssertions
     */
    public function it_creates_plans() 
    {
        $reflection = $this->reflected("created_plan");

        $reflection->invokeMethod(self::CREATE_PlAN);
    }

    /** 
     * @test
     * @doesNotPerformAssertions
     */
    public function it_fetches_plans() 
    {
        $reflection = $this->reflected();

        $reflection->invokeMethod(self::FETCH_PLAN, ["PLN_gx2wn530m0i3w3m"]);
    }

    /** 
     * @test
     * @doesNotPerformAssertions
     */
    public function it_updates_plans() 
    {
        $reflection = $this->reflected();

        $reflection->invokeMethod(self::UPDATE_PLAN, ["PLN_gx2wn530m0i3w3m"]);
    }

    /** @test */
    public function it_creates_customers ()
    {
        $reflection = $this->reflected("created_customers");

        $actual = $reflection->invokeMethod(self::CREATE_CUSTOMER);

        $expected = $this->getResource()["created_customers"];

        return $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** @test */
    public function it_fetches_customers ()
    {
        $reflection = $this->reflected("fetch_customers");

        $actual = $reflection->invokeMethod(self::FETCH_CUSTOMER, [1]);

        $expected = $this->getResource()["fetch_customers"];

        return $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    /** @test */
    public function it_updates_customers ()
    {
        $reflection = $this->reflected("update_customers");

        $actual = $reflection->invokeMethod(self::UPDATE_CUSTOMER, [1]);

        $expected = $this->getResource()["update_customers"];

        return $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    // /** 
    //  * @test
    //  * @dataProvider providers
    //  */
    // public function it_gets_infos ($expected, $actual) 
    // {
    //     $this->assertEquals($expected, $actual);
    // }

    // /**
    //  * Get elements from paystack.
    //  * 
    //  * @return array data to test.
    //  */
    // public function providers ()
    // {
    //      $elements = ["customers", "plans", "transactions"];

    //     return array_map(function($value){

    //         parent::setUp();

    //         $reflection = $this->reflected("all_{$value}");

    //         $actual = $reflection->invokeMethod(constant("self::GET_ALL_".strtoupper($value)));

    //         $expected = $this->getResource()["all_{$value}"]["data"];

    //         return [json_encode($expected), json_encode($actual)];

    //     }, $elements);
    // }
}
