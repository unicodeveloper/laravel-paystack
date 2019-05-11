<?php

declare(strict_types=1);

namespace  Unicodeveloper\Paystack\Test;

use Unicodeveloper\Paystack\PaystackFactory;
use Unicodeveloper\Paystack\PaystackManager;
use Xeviant\Paystack\Client;

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

    public function testBindings()
    {
        $this->assertIsInjectable(Client::class);

        $original = $this->app['paystack.connection'];
        $this->app['paystack']->reconnect();
        $new = $this->app['paystack.connection'];

        $this->assertNotSame($original, $new);
        $this->assertEquals($original, $new);
    }
}