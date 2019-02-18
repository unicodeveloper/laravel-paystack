<?php

declare(strict_types=1);

namespace Unicodeveloper\Paystack\Http;


use GrahamCampbell\CachePlugin\CachePlugin;
use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Psr\Cache\CacheItemPoolInterface;
use Xeviant\Paystack\HttpClient\Builder;

class ClientBuilder extends Builder
{
    public function addCache(CacheItemPoolInterface $cacheItemPool, array $config = [])
    {
        $this->setCachePlugin($cacheItemPool, $config['generator'] ?? null, $config['lifetime'] ?? null);

        $this->setPropertyValue('httpClientModified', true);
    }

    protected function setCachePlugin(CacheItemPoolInterface $cacheItemPool, CacheKeyGenerator $generator = null, int $lifetime = null): void
    {
        $stream = $this->getPropertyValue('streamFactory');

        $this->setPropertyValue('cachePlugin', new CachePlugin($cacheItemPool, $stream, $generator, $lifetime));
    }

    protected function getPropertyValue(string $name)
    {
        return static::getProperty($name)->getValue($this);
    }

    protected function setPropertyValue(string $name, $value)
    {
        return static::getProperty($name)->setValue($this, $value);
    }

    protected static function getProperty(string $name)
    {
        $prop = (new \ReflectionClass(Builder::class))->getProperty($name);

        $prop->setAccessible(true);

        return $prop;
    }
}