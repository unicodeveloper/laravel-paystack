<?php

declare(strict_types=1);

namespace Unicodeveloper\Paystack\Event;

use League\Event\EventInterface;
use Xeviant\Paystack\Event\EventPayload;

class EventHandler
{
    public function handle(EventInterface $event, EventPayload $payload): void
    {
        app('events')->dispatch($event->getName(), $payload);
    }
}
