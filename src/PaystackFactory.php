<?php

declare(strict_types=1);


namespace Unicodeveloper\Paystack;


use Unicodeveloper\Paystack\Http\ClientBuilder;
use Xeviant\Paystack\Client;

class PaystackFactory
{
    public function make(array $config)
    {
        $client = new Client();

        return $client;
    }

    protected function getBuilder()
    {
        return new ClientBuilder();
    }
}