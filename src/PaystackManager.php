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

namespace Unicodeveloper\Paystack;

/**
 * @method \Xeviant\Paystack\Api\Customers customers()
 * @method \Xeviant\Paystack\Api\Balance balance()
 * @method \Xeviant\Paystack\Api\Bank bank()
 * @method \Xeviant\Paystack\Api\BulkCharges bulkCharges()
 * @method \Xeviant\Paystack\Api\Bvn bvn()
 * @method \Xeviant\Paystack\Api\Charge charge()
 * @method \Xeviant\Paystack\Api\Integration integration()
 * @method \Xeviant\Paystack\Api\Invoices invoices()
 * @method \Xeviant\Paystack\Api\Pages pages()
 * @method \Xeviant\Paystack\Api\Plans plans()
 * @method \Xeviant\Paystack\Api\Refund refund()
 * @method \Xeviant\Paystack\Api\Settlements settlements()
 * @method \Xeviant\Paystack\Api\SubAccount subAccount()
 * @method \Xeviant\Paystack\Api\Subscriptions subscriptions()
 * @method \Xeviant\Paystack\Api\Transactions transactions()
 * @method \Xeviant\Paystack\Api\TransferRecipients transferRecipients()
 * @method \Xeviant\Paystack\Api\Transfers transfers()
 */


use GrahamCampbell\Manager\AbstractManager;
use Illuminate\Config\Repository;

class PaystackManager extends AbstractManager
{
    /**
     * @var PaystackFactory
     */
    private $factory;

    /**
     * PaystackManager constructor.
     *
     * @param Repository $repository
     * @param PaystackFactory $factory
     */
    public function __construct(Repository $repository, PaystackFactory $factory)
    {
        parent::__construct($repository);
        $this->factory = $factory;
    }

    /**
     * Create the connection instance.
     *
     * @param array $config
     *
     * @return \Xeviant\Paystack\Client
     */
    protected function createConnection(array $config)
    {
        return $this->factory->make($config);
    }

    /**
     * Get the configuration name.
     *
     * @return string
     */
    protected function getConfigName()
    {
        return 'paystack';
    }

    /**
     * Gets the instance of the Paystack Factory
     *
     * @return PaystackFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * {@inheritdoc}
     */
    public function __call(string $method, array $parameters)
    {
        $legacyObject = $this->getLegacyObject();

        if (method_exists($legacyObject, $method)) {
            return $legacyObject->{$method}(...$parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Gets the Legacy Paystack Object from v1 of this package
     *
     * @return Paystack
     */
    protected function getLegacyObject()
    {
        return new Paystack;
    }
}
