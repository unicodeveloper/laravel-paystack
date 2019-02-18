<?php

declare(strict_types=1);

namespace Unicodeveloper\Paystack\Test;


use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Factory;
use Mockery;
use Unicodeveloper\Paystack\PaystackFactory;
use Xeviant\Paystack\Client;
use Xeviant\Paystack\Exception\InvalidArgumentException;

final class PaystackFactoryTest extends AbstractTestBenchTestCase
{

    public function testIfFactoryCanBeCreatedWithMake()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['secret' => 'sk_123', 'public' => 'pk_123']);

        self::assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithCache()
    {
        $factory = $this->getFactory();

        $factory[1]->shouldRecieve('store')->once()->with(null)->andReturn(Mockery::mock(Repository::class));

        $client = $factory[0]->make(['secret' => 'sk_123', 'public' => 'pk_123']);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithApiUrl()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['secret' => 'sk_123', 'public' => 'pk_123', 'apiUrl' => 'https://api.example.co']);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithApiVersion()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['secret' => 'sk_123', 'public' => 'pk_123', 'apiVersion' => 'v2']);

        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     *
     */
    public function testMakeShouldFailIfKeysAreNotSet()
    {
        $this->expectException(InvalidArgumentException::class);
        $factory = $this->getFactory();

        $factory[0]->make([]);
    }


    protected function getFactory()
    {
        $cache = Mockery::mock(Factory::class);
        return [new PaystackFactory(), $cache];
    }
}