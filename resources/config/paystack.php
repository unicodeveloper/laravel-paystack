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

return [
    /**
     * Public Key From Paystack Dashboard
     *
     */
    'publicKey' => $publicKey = env('PAYSTACK_PUBLIC_KEY', 'publicKey'),

    /**
     * Secret Key From Paystack Dashboard
     *
     */
    'secretKey' => $secretKey = env('PAYSTACK_SECRET_KEY', 'secretKey'),

    /**
     * Paystack Payment URL
     *
     */
    'paymentUrl' => $paymentUrl = env('PAYSTACK_PAYMENT_URL'),

    /**
     * Optional email address of the merchant
     *
     */
    'merchantEmail' => $merchantEmail = env('MERCHANT_EMAIL'),

    'default' => 'main',

    'connections' => [
        'main' => [
            'publicKey'     => $publicKey,
            'secretKey'     => $secretKey,
            'paymentUrl'    => $paymentUrl
        ],

        'alternative' => [
            'publicKey'     => $publicKey,
            'secretKey'     => $secretKey,
            'paymentUrl'    => $paymentUrl
        ]
    ],
];
