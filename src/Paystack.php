<?php

namespace Unicodeveloper\Paystack;

use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Paystack {

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
     * @var mixed
     */
    protected $secretKey;

    /**
     * Instance of Client
     * @var object
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
        $this->baseUrl = config('paystack.paymentUrl');
    }

    /**
     * Get secret key from Paystack config file
     * @return  void
     */
    public function setKey()
    {
        $this->secretKey = config('paystack.secretKey');
    }

    /**
     * Set options for making the Client request
     * @return  void
     */
    private function setRequestOptions()
    {
        $authBearer = 'Bearer '. $this->secretKey;

        $this->client = new Client(['base_uri' => $this->baseUrl]);

        $this->client->setDefaultOption('headers', [
            'Authorization' => $authBearer,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json'
        ]);
    }

    /**
     * Initiate a payment request to Paystack
     * @return Unicodeveloper\Paystack\Paystack
     */
    public function makePaymentRequest()
    {
        $this->setResponse('/transaction/initialize');

        return $this;
    }

    /**
     * Make the client request and get the response
     * @param string $relativeUrl
     * @return Unicodeveloper\Paystack\Paystack
     */
    public function setResponse($relativeUrl)
    {
        $data = [
            "amount" => intval(request()->amount),
            "reference" => request()->reference,
            "email" => request()->email
        ];

        $this->response = $this->client->post($this->baseUrl . $relativeUrl, [
            'body' => json_encode($data)
        ]);

        return $this;
    }

    /**
     * Get the authorization url from the callback response
     * @return Unicodeveloper\Paystack\Paystack
     */
    public function getAuthorizationUrl()
    {
        $this->makePaymentRequest();

        $this->url = $this->response->json()["data"]["authorization_url"];

        return $this;
    }

    /**
     * Hit Paystack Gateway to Verify that the transaction is valid
     * @return void
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

        $result = $this->response->json()["message"];

        switch($result)
        {
            case self::VS:
                $validate = true;
                break;
            case self:ITF:
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
     * @throws Unicodeveloper\Paystack\Exceptions\PaymentVerificationFailedException
     * @return json
     */
    public function getPaymentData()
    {
        if($this->isTransactionVerificationValid()) {
            return $this->response->json();
        } else {
            throw new PaymentVerificationFailedException("Invalid Transaction Reference");
        }
    }

    /**
     * Fluent method to redirect to Paystack Payment Page
     * @return Illuminate\Support\Redirect
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
        return $this->response->json()["data"]["access_code"];
    }

    /**
     * Generate a Unique Transaction Reference
     * @return string
     */
    public function genTranxRef()
    {
        try {

                // Generate a version 1 (time-based) UUID object
                $uuid1 = Uuid::uuid1();
                echo $uuid1->toString() . "\n"; // i.e. e4eaaaf2-d142-11e1-b3e4-080027620cdd

                // // Generate a version 3 (name-based and hashed with MD5) UUID object
                // $uuid3 = Uuid::uuid3(Uuid::NAMESPACE_DNS, 'php.net');
                // echo $uuid3->toString() . "\n"; // i.e. 11a38b9a-b3da-360f-9353-a5a725514269

                // // Generate a version 4 (random) UUID object
                // $uuid4 = Uuid::uuid4();
                // echo $uuid4->toString() . "\n"; // i.e. 25769c6c-d34d-4bfe-ba98-e0ee856f3e7a

                // // Generate a version 5 (name-based and hashed with SHA1) UUID object
                // $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'php.net');
                // echo $uuid5->toString() . "\n"; // i.e. c4a760a8-dbcf-5254-a0d9-6a4474bd1b62

        } catch (UnsatisfiedDependencyException $e) {

            // Some dependency was not met. Either the method cannot be called on a
            // 32-bit system, or it can, but it relies on Moontoast\Math to be present.
            echo 'Caught exception: ' . $e->getMessage() . "\n";

        }
    }
}