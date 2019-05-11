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

namespace Unicodeveloper\Paystack\Http;


use GrahamCampbell\CachePlugin\CachePlugin;
use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Psr\Cache\CacheItemPoolInterface;
use Xeviant\Paystack\HttpClient\Builder;

class ClientBuilder extends Builder
{
    /**
     * Adds Cache Plugin to builder
     *
     * @param CacheItemPoolInterface $cacheItemPool
     * @param array $config
     * @throws \ReflectionException
     */
    public function addCache(CacheItemPoolInterface $cacheItemPool, array $config = [])
    {
        $this->setCachePlugin($cacheItemPool, $config['generator'] ?? null, $config['lifetime'] ?? null);

        $this->setPropertyValue('httpClientModified', true);
    }

    /**
     * Add a cache plugin to cache responses locally.
     *
     * @param CacheItemPoolInterface $cacheItemPool
     * @param CacheKeyGenerator|null $generator
     * @param int|null $lifetime
     * @throws \ReflectionException
     */
    protected function setCachePlugin(CacheItemPoolInterface $cacheItemPool, CacheKeyGenerator $generator = null, int $lifetime = null): void
    {
        $stream = $this->getPropertyValue('streamFactory');

        $this->setPropertyValue('cachePlugin', new CachePlugin($cacheItemPool, $stream, $generator, $lifetime));
    }

    /**
     * Retrieves the value of a builder property
     *
     * @param string $name
     * @return mixed
     * @throws \ReflectionException
     */
    protected function getPropertyValue(string $name)
    {
        return static::getProperty($name)->getValue($this);
    }

    /**
     * Sets the value of a builder property
     *
     * @param string $name
     * @param $value
     * @throws \ReflectionException
     */
    protected function setPropertyValue(string $name, $value)
    {
        return static::getProperty($name)->setValue($this, $value);
    }

    /**
     * Gets the builder reflection property for the given name
     *
     * @param string $name
     * @return \ReflectionProperty
     * @throws \ReflectionException
     */
    protected static function getProperty(string $name)
    {
        $prop = (new \ReflectionClass(Builder::class))->getProperty($name);

        $prop->setAccessible(true);

        return $prop;
    }
}