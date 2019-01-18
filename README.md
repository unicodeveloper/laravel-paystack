# Laravel Paystack
[![Latest Stable Version](https://poser.pugx.org/unicodeveloper/laravel-paystack/v/stable.svg)](https://packagist.org/packages/unicodeveloper/laravel-paystack)
[![License](https://poser.pugx.org/unicodeveloper/laravel-paystack/license.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/unicodeveloper/laravel-paystack.svg)](https://travis-ci.org/unicodeveloper/laravel-paystack)
[![Quality Score](https://img.shields.io/scrutinizer/g/unicodeveloper/laravel-paystack.svg?style=flat-square)](https://scrutinizer-ci.com/g/unicodeveloper/laravel-paystack)
[![Total Downloads](https://img.shields.io/packagist/dt/unicodeveloper/laravel-paystack.svg?style=flat-square)](https://packagist.org/packages/unicodeveloper/laravel-paystack)

> A Laravel 5 Package for working with Paystack seamlessly
## Installation

>[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.3+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Paystack, simply run
```bash
$ composer require unicodeveloper/laravel-paystack
```
Or 
Add the following line to the `require` block of your `composer.json` file.
```json
"unicodeveloper/laravel-paystack": "1.0.*"
```

Then  run
```bash
$ composer update
```
 This downloads it and updates the autoloader.

## Register Service Provider
 > If you use **Laravel >= 5.5** you can skip this step and go to [**`configuration`**](https://github.com/unicodeveloper/laravel-paystack#configuration)
 > 
Once **Laravel Paystack** is installed, you need to register the service provider. 
Open up `config/app.php` and add the following to the `providers` key.

 `Unicodeveloper\Paystack\PaystackServiceProvider::class`
 
Also, register the Facade like so:
```php
'aliases' => [
    ...
    'Paystack' => Unicodeveloper\Paystack\Facades\Paystack::class,
    ...
]
```
## Configuration

You can publish the configuration file using this command:

```
$ php artisan vendor:publish --provider="Unicodeveloper\Paystack\PaystackServiceProvider"
```
A configuration-file named `paystack.php` with some *sensible* defaults will be placed in your `config\` directory:

```php
<?php

return [

    /**
     * Public Key From Paystack Dashboard
     *
     */
    'publicKey' => getenv('PAYSTACK_PUBLIC_KEY'),

    /**
     * Secret Key From Paystack Dashboard
     *
     */
    'secretKey' => getenv('PAYSTACK_SECRET_KEY'),

    /**
     * Paystack Payment URL
     *
     */
    'paymentUrl' => getenv('PAYSTACK_PAYMENT_URL'),

    /**
     * Optional email address of the merchant
     *
     */
    'merchantEmail' => getenv('MERCHANT_EMAIL'),
    
   ];
```
then open your `.env` file and add your **Public Key, Secret Key, Merchant Email and Payment URL** as seen below:
```php
PAYSTACK_PUBLIC_KEY=xxxxxxxxxxxxx
PAYSTACK_SECRET_KEY=xxxxxxxxxxxxx
PAYSTACK_PAYMENT_URL=https://api.paystack.co
MERCHANT_EMAIL=yourmail@example.com
```

## General Payment Flow

Though there are multiple ways to pay an order, most payment gateways expect you to follow the following flow in your checkout process:

### 1. The customer is redirected to the payment provider
After the customer has gone through the checkout process and is ready to pay, the customer must be redirected to site of the payment provider.

The redirection is accomplished by submitting a form with some hidden fields. The form must post to the site of the payment provider. The hidden fields minimally specify the amount that must be paid, the order id and a hash.

The hash is calculated using the hidden form fields and a non-public secret. The hash is used by the payment provider to verify if the request is valid.


### 2. The customer pays on the site of the payment provider
The customer arrived on the site of the payment provider and gets to choose a payment method. All steps necessary to pay the order are taken care of by the payment provider.

### 3. The customer gets redirected back
After having paid the order the customer is redirected back. In the redirection request to the shop-site some values are returned. The values are usually the order id, a paymentresult and a hash.

The hash is calculated out of some of the fields returned and a secret non-public value. This hash is used to verify if the request is valid and comes from the payment provider. It is paramount that this hash is thoroughly checked.

## Usage

Set up your **Route** like so:

```php
// Laravel 5.1.17 and above
Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay'); 
```

OR

```php
Route::post('/pay', [
    'uses' => 'PaymentController@redirectToGateway',
    'as' => 'pay'
]);
```

```php
Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');
```

OR

```php
// Laravel 5.0
Route::get('payment/callback', [
    'uses' => 'PaymentController@handleGatewayCallback'
]); 
```

Set up your **Controller** like so:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Paystack; 
// use this if you will like to call methods directly i.e Paystack::methodname();

// use Unicodeveloper/Paystack/Paystack;
// If you use this then you have to create an instance i.e $paystack = new Paystack();

class PaymentController extends Controller
{
    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
	// After this method is called, $request carries all details submitted from form
	// Sensitive ones can be initialized in the controller as seen below.
	$request->email  =  Auth::user()->email;
	$request->amount  =  $netTotalAmount
	$request->reference  =  Paystack::genTranxRef();
	$request->key  =  config('paystack.secretKey');
	
        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        dd($paymentDetails);
        // Now you have the payment details,
        // you can store the authorization_code in your DB to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
    }
}
```

>Note: To avoid errors, make sure you have `/payment/callback` registered in Paystack Dashboard: [Here](https://dashboard.paystack.co/#/settings/developer) like so:

![payment-callback](https://cloud.githubusercontent.com/assets/2946769/12746754/9bd383fc-c9a0-11e5-94f1-64433fc6a965.png)
***(Optionally)*** Here is a sample **Payment Form:**

```html
<form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
		@csrf <!--Use this for Laravel 5.6 or higher OR-->
		{{ csrf_field() }} <!--Use this for Laravel 5.1 to 5.5 OR-->
		<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> <!--Use this for Laravel 5.0-->
		
        <div class="row" style="margin-bottom:40px;">
          <div class="col-md-8 col-md-offset-2">
            <p>
                <div>
                    Lagos Eyo Print Tee Shirt <strong>₦ 2,950</strong>
                </div>
            </p>
            <input type="hidden" name="orderID" value="345">
            <input type="hidden" name="quantity" value="3">
            <input type="hidden" name="metadata" value="{{ json_encode($array = ['key_name_1'=>'value_1', 'key_name_2'=>'value_2]) }}" > 
            <!-- For other necessary things you want to add to your payload. it is optional though -->
            <!-- All other sensitive information should be done in the Controller -->
            <p>
              <button class="btn btn-success btn-lg btn-block" type="submit" value="Pay Now!">
              <i class="fa fa-plus-circle fa-lg"></i> Pay Now!
              </button>
            </p>
          </div>
        </div>
</form>
```
When clicking the submit *( Pay Now! )* button the customer gets redirected to the Paystack site.

So now we've redirected the customer to Paystack. The customer did some actions there (hopefully he or she paid the order) and now gets redirected back to our shop site.

Paystack will redirect the customer to the url of the route that is specified in the Callback URL of the Web Hooks section on Paystack dashboard.

We must validate if the redirect to our site is a valid request (we don't want imposters to wrongfully place non-paid order).

In the controller that handles the request coming from the payment provider, we have

`Paystack::getPaymentData()` - This function calls the verification methods and ensure it is a valid transaction else it throws an exception.

You can test with this **Test Card** detail:
<br>**Card Number:** 4123450131001381
<br>**Expiry Date:** any date in the future
<br>**CVV:** 883

## Other usage
>Here is a brief documentation of the **fluent methods** in this package.
```php
Paystack::getAuthorizationUrl()->redirectNow();
```
 *  This fluent method does all the dirty work of sending a POST request with the form data to Paystack Api, then it gets the authorization Url and redirects the user to Paystack Payment Page. I abstracted all of it, so you don't have to worry about that. *Just eat your cookies while coding!*
 
```php
Paystack::getPaymentData();
```
 * This fluent method does all the dirty work of verifying that the just concluded transaction was actually valid, It verifies the transaction reference with Paystack Api and then grabs the data returned from Paystack. In that data, we have a lot of good stuff, especially the `authorization_code` that you can save in your DB to allow for easy recurrent subscription.

```php
Paystack::getAllCustomers();
```
 * This method gets all the customers that have performed transactions on your platform with Paystack.
  
```php
Paystack::getAllPlans();
```
 * This method gets all the plans that you have registered on Paystack.
 
```php
Paystack::getAllTransactions();
```
* This method gets all the transactions that have occurred.
 
```php
Paystack::genTranxRef();
```
 * This method generates a unique super secure cryptograhical hash token to use as transaction reference.

```php
Paystack::createSubAccount();
```
* This method creates a subaccount to be used for split payments.

```php
Paystack::fetchSubAccount();
```
* This method fetches the details of a subaccount.

```php
Paystack::listSubAccounts();
```
* This method lists the subaccounts associated with your paystack account.

```php
Paystack::updateSubAccount();
```
* This method Updates a subaccount to be used for split payments.

```php
Paystack::getRefPaymentData($trxref);
```
* This method gets the payment data of a transaction by passing its reference.

```php
Paystack::getPaymentStatus($trxref);
```
* This method gets the transaction status and gateway response by passing its reference.

## Todo

* Charge Returning Customers
* Add Comprehensive Tests
* Implement Transaction Dashboard to see all of the transactions in your laravel app

## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.

## How can I thank you?

Why not star the github repo? I'd love the attention! Why not share the link for this repository on Twitter or HackerNews? Spread the word!

Don't forget to [follow me on twitter](https://twitter.com/unicodeveloper)!

Thanks!
Prosper Otemuyiwa.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.