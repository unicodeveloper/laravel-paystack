<?php

declare(strict_types=1);

namespace  Unicodeveloper\Paystack\Test;

use Unicodeveloper\Paystack\PaystackFactory;
use Unicodeveloper\Paystack\PaystackManager;

class PaystackServiceProviderTest extends AbstractTestCase
{
    public function testIfPaystackFactoryIsInjectable()
    {
        $this->assertIsInjectable(PaystackFactory::class);
    }

    public function testIfPaystackManagerIsInjectable()
    {
        $this->assertIsInjectable(PaystackManager::class);
    }
}