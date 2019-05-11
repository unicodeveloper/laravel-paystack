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

        $client = $factory[0]->make(['secretKey' => 'sk_123', 'publicKey' => 'pk_123']);

        self::assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithCache()
    {
        $factory = $this->getFactory();

        $factory[1]->shouldReceive('store')->once()->with(null)->andReturn(Mockery::mock(Repository::class));

        $client = $factory[0]->make(['secretKey' => 'sk_123', 'publicKey' => 'pk_123', 'cache' => true]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithApiUrl()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['secretKey' => 'sk_123', 'publicKey' => 'pk_123', 'paymentUrl' => 'https://api.example.co']);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithApiVersion()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['secretKey' => 'sk_123', 'publicKey' => 'pk_123', 'apiVersion' => 'v2']);

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
        return [new PaystackFactory($cache), $cache];
    }
}