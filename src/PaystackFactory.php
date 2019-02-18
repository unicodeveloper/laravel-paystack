<?php

declare(strict_types=1);


namespace Unicodeveloper\Paystack;


use Illuminate\Contracts\Cache\Factory;
use Madewithlove\IlluminatePsrCacheBridge\Laravel\CacheItemPool;
use Unicodeveloper\Paystack\Http\ClientBuilder;
use Xeviant\Paystack\Client;

class PaystackFactory
{
    /**
     * Laravel Cache Instance
     *
     * @var Factory
     */
    private $cache;

    public function __construct(Factory $cache = null)
    {
        $this->cache = $cache;
    }

    public function make(array $config)
    {
        $client = new Client($this->getBuilder(), 'v1');

        return $client;
    }

    protected function getBuilder($config)
    {
        $builder = new ClientBuilder();

        if ($this->cache && class_exists(CacheItemPool::class) && $cache = array_get($config, 'cache')) {
            $builder->addCache(new CacheItemPool($this->cache->store( $cache === true ? null : $cache)));
        }

        return $builder;
    }
}