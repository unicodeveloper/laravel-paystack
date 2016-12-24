<?php

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unicodeveloper\Paystack;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Unicodeveloper\Paystack\Exceptions\IsNullException;
use Unicodeveloper\Paystack\Exceptions\PaymentVerificationFailedException;

class Paystack
{
    /**
     * Transaction Verification Successful
     */
    const VS = 'Verification successful';

    /**
     *  Invalid Transaction reference
     */
    const ITF = "Invalid transaction reference";

    /**
     * Issue Secret Key from your Paystack Dashboard
     * @var string
     */
    protected $secretKey;

    /**
     * Instance of Client
     * @var Client
     */
    protected $client;

    /**
     *  Response from requests made to Paystack
     * @var mixed
     */
    protected $response;

    /**
     * Paystack API base Url
     * @var string
     */
    protected $baseUrl;

    /**
     * Authorization Url - Paystack payment page
     * @var string
     */
    protected $authorizationUrl;

    public function __construct()
    {
        $this->setKey();
        $this->setBaseUrl();
        $this->setRequestOptions();
    }

    /**
     * Get Base Url from Paystack config file
     */
    public function setBaseUrl()
    {
        $this->baseUrl = Config::get('paystack.paymentUrl');
    }

    /**
     * Get secret key from Paystack config file
     */
    public function setKey()
    {
        $this->secretKey = Config::get('paystack.secretKey');
    }

    /**
     * Set options for making the Client request
     */
    private function setRequestOptions()
    {
        $authBearer = 'Bearer '. $this->secretKey;

        $this->client = new Client(
            [
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'Authorization' => $authBearer,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json'
                ]
            ]
        );
    }

    /**
     * Initiate a payment request to Paystack
     * @return Paystack
     */
    public function makePaymentRequest()
    {
        $data = [
            "amount" => intval(request()->amount),
            "reference" => request()->reference,
            "email" => request()->email,
            "plan" => request()->plan,
            "first_name" => request()->first_name,
            "last_name" => request()->last_name,
            "callback_url" => request()->callback_url,
            /*
            * to allow use of metadata on Paystack dashboard and a means to return additional data back to redirect url
            * form need an input field: <input type="hidden" name="metadata" value="{{ json_encode($array) }}" >
            *array must be set up as: $array = [ 'custom_fields' => [
            *                                                            ['display_name' => "Cart Id", "variable_name" => "cart_id", "value" => "2"],
            *                                                            ['display_name' => "Sex", "variable_name" => "sex", "value" => "female"],
            *                                                            .
            *                                                            .
            *                                                            .
            *                                                        ]
            *                                        
            *                                  ]
            */
            'metadata' => request()->metadata 
        ];

        // Remove the fields which were not sent (value would be null)
        array_filter($data);

        $this->setHttpResponse('/transaction/initialize', 'POST', $data);

        return $this;
    }


    /**
     * @param string $relativeUrl
     * @param string $method
     * @param array $body
     * @return Paystack
     * @throws IsNullException
     */
    private function setHttpResponse($relativeUrl, $method, $body = [])
    {
        if (is_null($method)) {
            throw new IsNullException("Empty method not allowed");
        }

        $this->response = $this->client->{strtolower($method)}(
            $this->baseUrl . $relativeUrl,
            ["body" => json_encode($body)]
        );

        return $this;
    }

    /**
     * Get the authorization url from the callback response
     * @return Paystack
     */
    public function getAuthorizationUrl()
    {
        $this->makePaymentRequest();

        $this->url = $this->getResponse()['data']['authorization_url'];

        return $this;
    }

    /**
     * Hit Paystack Gateway to Verify that the transaction is valid
     */
    private function verifyTransactionAtGateway()
    {
        $transactionRef = request()->query('trxref');

        $relativeUrl = "/transaction/verify/{$transactionRef}";

        $this->response = $this->client->get($this->baseUrl . $relativeUrl, []);
    }

    /**
     * True or false condition whether the transaction is verified
     * @return boolean
     */
    public function isTransactionVerificationValid()
    {
        $this->verifyTransactionAtGateway();

        $result = $this->getResponse()['message'];

        switch ($result) {
            case self::VS:
                $validate = true;
                break;
            case self::ITF:
                $validate = false;
                break;
            default:
                $validate = false;
                break;
        }

        return $validate;
    }

    /**
     * Get Payment details if the transaction was verified successfully
     * @return json
     * @throws PaymentVerificationFailedException
     */
    public function getPaymentData()
    {
        if ($this->isTransactionVerificationValid()) {
            return $this->getResponse();
        } else {
            throw new PaymentVerificationFailedException("Invalid Transaction Reference");
        }
    }

    /**
     * Fluent method to redirect to Paystack Payment Page
     */
    public function redirectNow()
    {
        return redirect($this->url);
    }

    /**
     * Get Access code from transaction callback respose
     * @return string
     */
    public function getAccessCode()
    {
        return $this->getResponse()['data']['access_code'];
    }

    /**
     * Generate a Unique Transaction Reference
     * @return string
     */
    public function genTranxRef()
    {
        return TransRef::getHashedToken();
    }

    /**
     * Get all the customers that have made transactions on your platform
     * @return array
     */
    public function getAllCustomers()
    {
        $this->setRequestOptions();

        return $this->setHttpResponse("/customer", 'GET', [])->getData();
    }

    /**
     * Get all the plans that you have on Paystack
     * @return array
     */
    public function getAllPlans()
    {
        $this->setRequestOptions();

        return $this->setHttpResponse("/plan", 'GET', [])->getData();
    }

    /**
     * Get all the transactions that have happened overtime
     * @return array
     */
    public function getAllTransactions()
    {
        $this->setRequestOptions();

        return $this->setHttpResponse("/transaction", 'GET', [])->getData();
    }

    /**
     * Get the whole response from a get operation
     * @return array
     */
    private function getResponse()
    {
        return json_decode($this->response->getBody(), true);
    }

    /**
     * Get the data response from a get operation
     * @return array
     */
    private function getData()
    {
        return $this->getResponse()['data'];
    }

    /**
     * Create a plan
     */
    public function createPlan()
    {
        $data = [
            "name" => request()->name,
            "description" => request()->desc,
            "amount" => intval(request()->amount),
            "interval" => request()->interval,
            "send_invoices" => request()->send_invoices,
            "send_sms" => request()->send_sms,
            "currency" => request()->currency,
        ];

        $this->setRequestOptions();

        $this->setHttpResponse("/plan", 'POST', $data);

    }

    /**
     * Fetch any plan based on its plan id or code
     * @param $plan_code
     * @return array
     */
    public function fetchPlan($plan_code)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/plan/' . $plan_code, 'GET', [])->getResponse();
    }

    /**
     * Update any plan's details based on its id or code
     * @param $plan_code
     * @return array
     */
    public function updatePlan($plan_code)
    {
        $data = [
            "name" => request()->name,
            "description" => request()->desc,
            "amount" => intval(request()->amount),
            "interval" => request()->interval,
            "send_invoices" => request()->send_invoices,
            "send_sms" => request()->send_sms,
            "currency" => request()->currency,
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/plan/' . $plan_code, 'PUT', $data)->getResponse();
    }

    /**
     * Create a customer
     */
    public function createCustomer()
    {
        $data = [
            "email" => request()->email,
            "first_name" => request()->fname,
            "last_name" => request()->lname,
            "phone" => request()->phone,
            "metadata" => request()->additional_info /* key => value pairs array */

        ];

        $this->setRequestOptions();
        $this->setHttpResponse('/customer', 'POST', $data);
    }

    /**
     * Fetch a customer based on id or code
     * @param $customer_id
     * @return array
     */
    public function fetchCustomer($customer_id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/customer/'. $customer_id, 'GET', [])->getResponse();
    }

    /**
     * Update a customer's details based on their id or code
     * @param $customer_id
     * @return array
     */
    public function updateCustomer($customer_id)
    {
        $data = [
            "email" => request()->email,
            "first_name" => request()->fname,
            "last_name" => request()->lname,
            "phone" => request()->phone,
            "metadata" => request()->additional_info /* key => value pairs array */

        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/customer/'. $customer_id, 'PUT', $data)->getResponse();
    }

    /**
     * Export transactions in .CSV
     * @return array
     */
    public function exportTransactions()
    {
        $data = [
            "from" => request()->from,
            "to" => request()->to,
            'settled' => request()->settled
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/transaction/export', 'GET', $data)->getResponse();
    }

    /**
     * Create a subscription to a plan from a customer.
     */
    public function createSubscription()
    {
        $data = [
            "customer" => request()->customer, //Customer email or code
            "plan" => request()->plan,
            "authorization" => request()->authorization_code
        ];

        $this->setRequestOptions();
        $this->setHttpResponse('/subscription', 'POST', $data);
    }

    /**
     * Enable a subscription using the subscription code and token
     * @return array
     */
    public function enableSubscription()
    {
        $data = [
            "code" => request()->code,
            "token" => request()->token,
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/subscription/enable', 'POST', $data)->getResponse();
    }

    /**
     * Disable a subscription using the subscription code and token
     * @return array
     */
    public function disableSubscription()
    {
        $data = [
            "code" => request()->code,
            "token" => request()->token,
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/subscription/disable', 'POST', $data)->getResponse();
    }

    /**
     * Fetch details about a certain subscription
     * @param mixed $subscription_id
     * @return array
     */
    public function fetchSubscription($subscription_id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/subscription/'.$subscription_id, 'GET', [])->getResponse();
    }

    /**
     * Create pages you can share with users using the returned slug
     */
    public function createPage()
    {
        $data = [
            "name" => request()->name,
            "description" => request()->description,
            "amount" => request()->amount
        ];

        $this->setRequestOptions();
        $this->setHttpResponse('/page', 'POST', $data);
    }

    /**
     * Fetches all the pages the merchant has
     * @return array
     */
    public function getAllPages()
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/page', 'GET', [])->getResponse();
    }

    /**
     * Fetch details about a certain page using its id or slug
     * @param mixed $page_id
     * @return array
     */
    public function fetchPage($page_id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/page/'.$page_id, 'GET', [])->getResponse();
    }

    /**
     * Update the details about a particular page
     * @param $page_id
     * @return array
     */
    public function updatePage($page_id)
    {
        $data = [
            "name" => request()->name,
            "description" => request()->description,
            "amount" => request()->amount
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/page/'.$page_id, 'PUT', $data)->getResponse();
    }
}
