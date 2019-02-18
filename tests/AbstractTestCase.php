<?php
declare(strict_types=1);

namespace  Unicodeveloper\Paystack\Test;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Unicodeveloper\Paystack\PaystackServiceProvider;

class AbstractTestCase extends AbstractPackageTestCase
{
    protected function getServiceProviderClass($app)
    {
        return PaystackServiceProvider::class;
    }
}