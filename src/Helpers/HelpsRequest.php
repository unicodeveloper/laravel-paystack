<?php

namespace Unicodeveloper\Paystack\Helpers;

trait HelpsRequest {
    public function addRequestData(array $args)
    {
        foreach ($args as $key => $data) {
            request()->request->add([$key => $data]);
        }
    }
}
