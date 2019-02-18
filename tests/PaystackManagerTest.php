<?php

declare(strict_types=1);

namespace Unicodeveloper\Paystack\Test;


use Illuminate\Config\Repository;
use Mockery;
use Unicodeveloper\Paystack\PaystackFactory;
use Unicodeveloper\Paystack\PaystackManager;
use Xeviant\Paystack\Client;

class PaystackManagerTest extends AbstractTestCase
{
    public function testCreateConnection()
    {
        $config = ['secret_key' => 'sk_123abc', 'public_key' => 'pk_123abc'];

        $manager = $this->getManager($config);

        $manager->getConfig()->shouldReceive('get')->once()
            ->with('paystack.default')->andReturn('main');

        $this->assertSame([], $manager->getConnections());

        $return = $manager->connection();

        $this->assertInstanceOf(Client::class, $return);

        $this->assertArrayHasKey('main', $manager->getConnections());
    }

    protected function getManager(array $config)
    {
        $repo = Mockery::mock(Repository::class);
        $factory = Mockery::mock(PaystackFactory::class);


        $manager = new PaystackManager($repo, $factory);

        $manager->getConfig()->shouldReceive('get')->once()
            ->with('paystack.connections')->andReturn(['main' => $config]);

        $config['name'] = 'main';

        $manager->getFactory()->shouldReceive('make')->once()
            ->with($config)->andReturn(Mockery::mock(Client::class));

        return $manager;
    }
}