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

    protected $expected;
    protected $actual;

    // Methods to test.
    const SET_KEY = "setKey";
    const FETCH_PAGE = "fetchPage";
    const FETCH_PLAN = "fetchPlan";
    const CREATE_PlAN = "createPlan";
    const UPDATE_PAGE = "updatePage";
    const CREATE_PAGE = "createPage";
    const UPDATE_PLAN = "updatePlan";
    const REDIRECT_NOW = "redirectNow";
    const GET_RESPONSE = "getResponse";
    const GEN_TRANX_REF = "genTranxRef";
    const GET_ALL_PAGES = "getAllPages";
    const GET_ALL_PLANS = "getAllPlans";
    const FETCH_CUSTOMER = "fetchCustomer";
    const GET_ACCESS_CODE = "getAccessCode";
    const CREATE_CUSTOMER = "createCustomer";
    const UPDATE_CUSTOMER = "updateCustomer";
    const GET_PAYMENT_DATA = "getPaymentData";
    const LIST_SUBACCOUNTS = "listSubAccounts";
    const FETCH_SUBACCOUNT = "fetchSubAccount";
    const GET_ALL_CUSTOMERS = "getAllCustomers";
    const SET_HTTP_RESPONSE = "setHttpResponse";
    const CREATE_SUBACCOUNT = "createSubAccount";
    const UPDATE_SUBACCOUNT = "updateSubAccount";
    const FETCH_SUBSCRIPTION = "fetchSubscription";
    const ENABLE_SUBSCRIPTION = "enableSubscription";
    const EXPORT_TRANSACTIONS = "exportTransactions";
    const CREATE_SUBSCRIPTION = "createSubscription";
    const GET_ALL_TRANSACTIONS = "getAllTransactions";
    const MAKE_PAYMENT_REQUEST = "makePaymentRequest";
    const DISABLE_SUBSCRIPTION = "disableSubscription";
    const GET_AUTHORIZATION_URL = "getAuthorizationUrl";
    const GET_ALL_SUBSCRIPTIONS = "getAllSubscriptions";
    const GET_PLAN_SUBSCRIPTIONS = "getPlanSubscriptions";
    const GET_CUSTOMER_SUBSCRIPTIONS = "getCustomerSubscriptions";
    const GET_AUTHORIZATION_RESPONSE = "getAuthorizationResponse";
    const VERIFY_TRANSACTION_AT_GATEWAY = "verifyTransactionAtGateway";
    const IS_TRANSACTION_VERIFICATION_VALID = "isTransactionVerificationValid";

    // Properties
    const SECRET_KEY = "secretKey";
    const AUTHORIZATION_URL = "authorizationUrl";

    /** @test */
    public function it_initiated_properly () 
    {
        $paystack = $reflection = $this->reflected();

        $reflection->invokeMethod(self::SET_KEY);

        $secretKey = $reflection->fetchProperty(self::SECRET_KEY);

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
    public function it_sets_http_response (Reflectors $reflection)
    {
        $reflection->invokeMethod(self::SET_HTTP_RESPONSE, ["/", "POST"]);

        $this->responseIsPsr7($reflection);
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

        $this->actual = $reflection->fetchProperty(self::AUTHORIZATION_URL)->value;

        $keys = ["payment_response", "data", "authorization_url"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_gets_authorization_response ()
    {
        $reflection = $this->reflected();

        $response = $reflection->invokeMethod(self::GET_AUTHORIZATION_RESPONSE);

        $this->assertInternalType("array", $response);
    }

    /** @test */
    public function it_gets_response ()
    {
        $reflection = $this->reflected();

        $reflection->setProperty("response", $this->response("payment_response"));

        $response = $reflection->invokeMethod(self::GET_RESPONSE);

        $this->assertInternalType("array", $response);
    }

    /** @test */
    public function it_verifies_transaction_at_gateway ()
    {
        $reflection = $this->reflected("validation_response_success");

        $reflection->invokeMethod(self::VERIFY_TRANSACTION_AT_GATEWAY);

        $this->responseIsPsr7($reflection);
    }

    /** @test */
    public function it_verifies_transaction_validity_success ()
    {
        $reflection = $this->reflected("validation_response_success");

        $valid = $reflection->invokeMethod(self::IS_TRANSACTION_VERIFICATION_VALID);

        $this->assertTrue($valid);
    }

    /** @test */
    public function it_verifies_transaction_validity_false ()
    {
        $reflection = $this->reflected("validation_response_invalid");

        $valid = $reflection->invokeMethod(self::IS_TRANSACTION_VERIFICATION_VALID);

        $this->assertFalse($valid);
    }

    /** @test */
    public function it_verifies_transaction_validity_other ()
    {
        $reflection = $this->reflected("validation_response_other");

        $valid = $reflection->invokeMethod(self::IS_TRANSACTION_VERIFICATION_VALID);

        $this->assertFalse($valid);
    }

     /** @test */
    public function it_gets_payment_data ()
    {
        $resource = $this->getResource();

        $this->expected = $resource["validation_response_success"];

        $reflection = $this->reflected("validation_response_success");

        $data = $reflection->invokeMethod(self::GET_PAYMENT_DATA);

        $this->actual = $data;

        $this->checkEquals();
    }

    /**
     * @test
     * @expectedException Unicodeveloper\Paystack\Exceptions\PaymentVerificationFailedException
     */
    public function it_gets_payment_data_invalid_trans ()
    {
        $reflection = $this->reflected("validation_response_invalid");

        $reflection->invokeMethod(self::GET_PAYMENT_DATA);
    }

    /** 
     * @test
     * @return Tests\Concerns\Reflectors $reflection
     */
    public function it_redirects_now ()
    {
        $reflection = $this->reflected();

        $redirectNow = $reflection->invokeMethod(self::REDIRECT_NOW);

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

        $this->actual = $reflection->invokeMethod(self::GET_ACCESS_CODE);

        $keys = ["payment_response", "data", "access_code"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** 
     * @test
     * @depends it_redirects_now
     */
    public function it_gens_trans_ref (Reflectors $reflection)
    {
        $ref = $reflection->invokeMethod(self::GEN_TRANX_REF);

        $this->assertCount(25, str_split($ref));
    }

    /** @test */
    public function it_gets_all_customers()
    {
        $reflection = $this->reflected("all_customers");

        $this->actual = $reflection->invokeMethod(self::GET_ALL_CUSTOMERS);

        $keys = ["all_customers", "data"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_gets_all_plans ()
    {
        $reflection = $this->reflected("all_plans");

        $this->actual = $reflection->invokeMethod(self::GET_ALL_PLANS);

        $keys = ["all_plans", "data"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_gets_all_transactions () 
    {
        $reflection = $this->reflected("all_transactions");

        $this->actual = $reflection->invokeMethod(self::GET_ALL_TRANSACTIONS);

        $keys = ["all_transactions", "data"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
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

        $this->actual = $reflection->invokeMethod(self::CREATE_CUSTOMER);

        $keys = ["created_customers"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_fetches_customers ()
    {
        $reflection = $this->reflected("fetch_customers");

        $this->actual = $reflection->invokeMethod(self::FETCH_CUSTOMER, [1]);

        $keys = ["fetch_customers"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_updates_customers ()
    {
        $reflection = $this->reflected("update_customers");

        $this->actual = $reflection->invokeMethod(self::UPDATE_CUSTOMER, [1]);

        $keys = ["update_customers"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_exports_transactions ()
    {
        $reflection = $this->reflected("export_transactions");

        $this->actual = $reflection->invokeMethod (self::EXPORT_TRANSACTIONS);

        $keys = ["export_transactions"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** 
     * @test
     * @doesNotPerformAssertions
     */
    public function it_creates_subscriptions ()
    {
        $reflection = $this->reflected ("created_subscription");

        $reflection->invokeMethod(self::CREATE_SUBSCRIPTION);
    }

    /** @test */
    public function it_gets_all_subscription () 
    {
        $reflection = $this->reflected ("all_subscriptions");

        $this->actual = $reflection->invokeMethod(self::GET_ALL_SUBSCRIPTIONS);

        $keys = ["all_subscriptions", "data"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** 
     * @test
     * @doesNotPerformAssertions
     */
    public function it_gets_customer_subscriptions ()
    {
        $reflection = $this->reflected ();

        $this->actual = $reflection->invokeMethod(self::GET_CUSTOMER_SUBSCRIPTIONS, [1]);
    }

    /** 
     * @test
     * @doesNotPerformAssertions
     */
    public function it_gets_plan_subscription ()
    {
        $reflection = $this->reflected ();

        $this->actual = $reflection->invokeMethod(self::GET_PLAN_SUBSCRIPTIONS, [1]);
    }

    /** @test */
    public function it_enables_subscription ()
    {
        $reflection = $this->reflected ("enabled_subscription");

        $this->actual = $reflection->invokeMethod(self::ENABLE_SUBSCRIPTION);

        $keys = ["enabled_subscription"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_disables_subscription ()
    {
        $reflection = $this->reflected ("disabled_subscription");

        $this->actual = $reflection->invokeMethod(self::DISABLE_SUBSCRIPTION);

        $keys = ["disabled_subscription"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_fetches_subscription () 
    {
        $reflection = $this->reflected ("fetch_subscription");

        $this->actual = $reflection->invokeMethod(self::FETCH_SUBSCRIPTION, ["subscription_id"]);

        $keys = ["fetch_subscription"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_creates_page () 
    {
        $reflection = $this->reflected("created_page");

        $reflection->invokeMethod(self::CREATE_PAGE);

        $this->responseIsPsr7($reflection);
    }

    /** @test */
    public function it_gets_all_page () 
    {
        $reflection = $this->reflected("all_pages");

        $this->actual = $reflection->invokeMethod(self::GET_ALL_PAGES);

        $keys = ["all_pages"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_fetches_page () 
    {
        $reflection = $this->reflected("fetched_page");

        $this->actual = $reflection->invokeMethod(self::FETCH_PAGE, ["page_id"]);

        $keys = ["fetched_page"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_updates_page () 
    {
        $reflection = $this->reflected("updated_page");

        $this->actual = $reflection->invokeMethod(self::UPDATE_PAGE, ["page_id"]);

        $keys = ["updated_page"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_creates_sub_accounts () 
    {
        $reflection = $this->reflected("created_subaccount");

        $this->actual = $reflection->invokeMethod(self::CREATE_SUBACCOUNT);

        $keys = ["created_subaccount"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_fetch_sub_accounts () 
    {
        $reflection = $this->reflected("fetched_subaccount");

        $this->actual = $reflection->invokeMethod(self::FETCH_SUBACCOUNT, ["subaccount_code"]);

        $keys = ["fetched_subaccount"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_gets_all_sub_accounts () 
    {
        $reflection = $this->reflected("all_subaccount");

        $this->actual = $reflection->invokeMethod(self::LIST_SUBACCOUNTS, [20, 1]);

        $keys = ["all_subaccount"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /** @test */
    public function it_updates_sub_accounts () 
    {
        $reflection = $this->reflected("updated_subaccount");

        $this->actual = $reflection->invokeMethod(self::UPDATE_SUBACCOUNT, ["account_id"]);

        $keys = ["updated_subaccount"];

        $this->expected = $this->getExpected(...$keys);

        $this->checkEquals();
    }

    /**
     * Performs checks of expected against actual.
     * 
     * @return void
     */
    public function checkEquals()
    {
        $this->assertEquals(json_encode($this->expected), json_encode($this->actual));
    }

    /**
     * Get specific resource to be tested.
     * 
     * @param  array $keys Relative keys
     * @return mixed
     */
    public function getExpected (&...$keys)
    {
        $response = $this->getResource();

        return array_reduce($keys, function($carry, $value) {

            return $carry[$value];

        }, $response);
    }

    public function responseIsPsr7(Reflectors $reflection)
    {
        $response = $reflection->fetchProperty("response");

        $this->assertInstanceOf("GuzzleHttp\Psr7\Response", $response->value);
    }
}
