<?php

declare(strict_types=1);

namespace Unicodeveloper\Paystack\Test;


use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use Unicodeveloper\Paystack\PaystackFactory;
use Xeviant\Paystack\Client;

final class PaystackFactoryTest extends AbstractTestBenchTestCase
{

    public function testIfFactoryCanBeCreatedWithMake()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['secret' => 'sk_123', 'public' => 'pk_123']);

        self::assertInstanceOf(Client::class, $client);
    }


    protected function getFactory()
    {
        return [new PaystackFactory()];
    }
}