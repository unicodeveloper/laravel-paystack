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

namespace Unicodeveloper\Paystack;

use GuzzleHttp\Client;
use Unicodeveloper\Paystack\Exceptions\PaymentVerificationFailedException;

class Paystack
{
    /**
     * Transaction Verification Successful
     */
    const VERIFICATION_SUCCESSFUL = 'Verification successful';

    /**
     *  Invalid Transaction reference
     */
    const INVALID_TRANSACTION_REFERENCE = "Invalid transaction reference";

    /**
     *  Response from requests made to Paystack
     * @var array
     */
    protected $response;

    /**
     * Authorization Url - Paystack payment page
     * @var string
     */
    protected $authorizationUrl;

    /**
     * @var \Xeviant\Paystack\Client
     */
    private $paystack;

    /**
     * Authorization URL
     *
     * @var string
     */
    private $url;

    /**
     * Paystack constructor.
     */
    public function __construct()
    {
        $this->paystack = app()->make('paystack.connection');
    }


    /**
     * Initiate a payment request to Paystack
     * Included the option to pass the payload to this method for situations
     * when the payload is built on the fly (not passed to the controller from a view)
     * @param null $data
     * @return Paystack
     */
    public function makePaymentRequest($data = null)
    {
        if ( $data == null ) {
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
        }

        $this->response = $this->paystack->transactions()->initialize($data);

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
     * Get the authorization callback response
     * In situations where Laravel serves as an backend for a detached UI, the api cannot redirect
     * and might need to take different actions based on the success or not of the transaction
     * @param $data
     * @return array
     */
    public function getAuthorizationResponse($data)
    {
        $this->makePaymentRequest($data);

        $this->url = $this->getResponse()['data']['authorization_url'];

        return $this->getResponse();
    }

    /**
     * Hit Paystack Gateway to Verify that the transaction is valid
     */
    private function verifyTransactionAtGateway()
    {
        $transactionRef = request()->query('trxref');

        $this->response = $this->paystack->transactions()->verify($transactionRef);
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
            case self::VERIFICATION_SUCCESSFUL:
                $validate = true;
                break;
            case self::INVALID_TRANSACTION_REFERENCE:
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
     * @return array
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
     * Get Access code from transaction callback response
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
        return $this->paystack->cutsomers()->list();
    }

    /**
     * Get all the plans that you have on Paystack
     * @return array
     */
    public function getAllPlans()
    {
        return $this->paystack->plans()->list();
    }

    /**
     * Get all the transactions that have happened overtime
     * @return array
     */
    public function getAllTransactions()
    {
        return $this->paystack->transactions()->list();
    }

    /**
     * Get the whole response from a get operation
     * @return array
     */
    private function getResponse()
    {
        return $this->response;
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

        return $this->paystack->plans()->create($data);
    }

    /**
     * Fetch any plan based on its plan id or code
     * @param $planCode
     * @return array
     */
    public function fetchPlan($planCode)
    {
        return $this->paystack->plans()->fetch($planCode);
    }

    /**
     * Update any plan's details based on its id or code
     * @param $planCode
     * @return array
     */
    public function updatePlan($planCode)
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

       return $this->paystack->plans()->update($planCode, $data);
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

        return $this->paystack->customers()->create($data);
    }

    /**
     * Fetch a customer based on id or code
     * @param $customerId
     * @return array
     */
    public function fetchCustomer($customerId)
    {
        return $this->paystack->customers()->fetch($customerId);
    }

    /**
     * Update a customer's details based on their id or code
     * @param $customerId
     * @return array
     */
    public function updateCustomer($customerId)
    {
        $data = [
            "email" => request()->email,
            "first_name" => request()->fname,
            "last_name" => request()->lname,
            "phone" => request()->phone,
            "metadata" => request()->additional_info /* key => value pairs array */

        ];

        return $this->paystack->customers()->update($customerId, $data);
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

        return $this->paystack->transactions()->export($data);
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

        return $this->paystack->subscriptions()->create($data);
    }

    /**
     * Get all the subscriptions made on Paystack.
     *
     * @return array
     */
    public function getAllSubscriptions()
    {
        return $this->paystack->subscriptions()->list();
    }

    /**
     * Get customer subscriptions
     *
     * @param integer $customerId
     * @return array
     */
    public function getCustomerSubscriptions($customerId)
    {
        return $this->paystack->subscriptions()->list(['customer' => $customerId]);
    }

    /**
     * Get plan subscriptions
     *
     * @param  integer $planId
     * @return array
     */
    public function getPlanSubscriptions($planId)
    {
        return $this->paystack->subscriptions()->list(['plan' => $planId]);
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

        return $this->paystack->subscrptions()->enable($data);
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

        return $this->paystack->subscriptions()->disabled($data);
    }

    /**
     * Fetch details about a certain subscription
     * @param mixed $subscriptionId
     * @return array
     */
    public function fetchSubscription($subscriptionId)
    {
        return $this->paystack->subscriptions()->fetch($subscriptionId);
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

        return $this->paystack->pages()->create($data);
    }

    /**
     * Fetches all the pages the merchant has
     * @return array
     */
    public function getAllPages()
    {
        return $this->paystack->pages()->list();
    }

    /**
     * Fetch details about a certain page using its id or slug
     * @param mixed $pageId
     * @return array
     */
    public function fetchPage($pageId)
    {
        return $this->paystack->pages()->fetch($pageId);
    }

    /**
     * Update the details about a particular page
     * @param $pageId
     * @return array
     */
    public function updatePage($pageId)
    {
        $data = [
            "name" => request()->name,
            "description" => request()->description,
            "amount" => request()->amount
        ];

        return $this->paystack->pages()->update($pageId, $data);
    }

    /**
     * Creates a subaccount to be used for split payments . Required    params are business_name , settlement_bank , account_number ,   percentage_charge
     *
     * @return array
     */

    public function createSubAccount()
    {
        $data = [
            "business_name" => request()->business_name,
            "settlement_bank" => request()->settlement_bank,
            "account_number" => request()->account_number,
            "percentage_charge" => request()->percentage_charge,
            "primary_contact_email" => request()->primary_contact_email,
            "primary_contact_name" => request()->primary_contact_name,
            "primary_contact_phone" => request()->primary_contact_phone,
            "metadata" => request()->metadata,
            'settlement_schedule' => request()->settlement_schedule
        ];

        return $this->paystack->subAccount()->create($data);
    }

    /**
     * Fetches details of a subaccount
     * @param subaccount code
     * @return array
     */
    public function fetchSubAccount($subAccountCode)
    {
        return $this->paystack->subAccount()->fetch($subAccountCode);
    }

    /**
     * Lists all the subaccounts associated with the account
     * @param $perPage - Specifies how many records to retrieve per page , $page - SPecifies exactly what page to retrieve
     * @param $page
     * @return array
     */
    public function listSubAccounts($perPage = null, $page = null)
    {
        return $this->paystack->subAccount()->list(['perPage' => $perPage, 'page' => $page]);
    }


    /**
     * Updates a sub-account to be used for split payments . Required params are business_name , settlement_bank , account_number , percentage_charge
     * @param sub-account code
     * @return array
     */

    public function updateSubAccount($subAccountCode)
    {
        $data = [
            "business_name" => request()->business_name,
            "settlement_bank" => request()->settlement_bank,
            "account_number" => request()->account_number,
            "percentage_charge" => request()->percentage_charge,
            "description" => request()->description,
            "primary_contact_email" => request()->primary_contact_email,
            "primary_contact_name" => request()->primary_contact_name,
            "primary_contact_phone" => request()->primary_contact_phone,
            "metadata" => request()->metadata,
            'settlement_schedule' => request()->settlement_schedule
        ];

        return $this->paystack->subAccount()->update($subAccountCode, $data);
    }
}