<?php

namespace Unicodeveloper\Quotes\Facades;

use Illuminate\Support\Facades\Facade;

class QuotesFacade extends Facade {
    /**
   * Get the registered name of the component.
   *
   * @return string
   */
    protected static function getFacadeAccessor()
    {
        return 'laravel-quotes';
    }
}