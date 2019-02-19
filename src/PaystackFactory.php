<?php

declare(strict_types=1);


namespace Unicodeveloper\Paystack;


use Illuminate\Contracts\Cache\Factory;
use Madewithlove\IlluminatePsrCacheBridge\Laravel\CacheItemPool;
use Unicodeveloper\Paystack\Http\ClientBuilder;
use Xeviant\Paystack\Client;
use Xeviant\Paystack\Config;
use Xeviant\Paystack\Exception\InvalidArgumentException;

class PaystackFactory
{
    /**
     * Laravel Cache Instance
     *
     * @var Factory
     */
    private $cache;

    /**
     * PaystackFactory constructor.
     *
     * @param Factory|null $cache
     */
    public function __construct(Factory $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Creates A Paystack Client Object
     *
     * @param array $config
     * @return Client
     */
    public function make(array $config)
    {
        if ($this->secretKeyDoesNotExist($config)) {
            throw new InvalidArgumentException('You cannot use the Paystack Factory without a SECRET key, go into "paystack.php" to set one.');
        }

        $compatibleConfig = $this->createCompatibleConfiguration($config);

        $client = new Client($this->getBuilder($config), 'v1', $compatibleConfig);

        return $client;
    }

    /**
     * Check to see if Secret key doesn't exists
     *
     * @param array $config
     * @return bool
     */
    protected function secretKeyDoesNotExist(array $config)
    {
        return !array_key_exists('secretKey', $config) || (isset($config['secretKey']) && empty($config['secretKey']));
    }

    /**
     * Creates a Compatible Paystack Client Configuration from a configuration array
     *
     * @param array $config
     * @return Config
     */
    public function createCompatibleConfiguration(array $config)
    {
        return new Config(null, $config['publicKey'] ?: null, $config['secretKey'] ?: null, 'v1');
    }

    /**
     * Prepares and retrieves the Paystack client builder
     *
     * @param $config
     * @return ClientBuilder
     */
    protected function getBuilder($config)
    {
        $builder = new ClientBuilder();

        if ($this->cache && class_exists(CacheItemPool::class) && $cache = array_get($config, 'cache')) {
            $builder->addCache(new CacheItemPool($this->cache->store( $cache === true ? null : $cache)));
        }

        return $builder;
    }
}
