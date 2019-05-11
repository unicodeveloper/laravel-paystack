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


namespace Unicodeveloper\Paystack\Test\Facades;

use GrahamCampbell\TestBenchCore\FacadeTrait;
use Unicodeveloper\Paystack\Facades\Paystack;
use Unicodeveloper\Paystack\PaystackManager;
use Unicodeveloper\Paystack\Test\AbstractTestCase;

/**
 * This is the github facade test class.
 */
class PaystackTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return 'paystack';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return Paystack::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return PaystackManager::class;
    }
}
