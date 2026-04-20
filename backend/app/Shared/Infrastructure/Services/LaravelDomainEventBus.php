<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Services;

use App\Shared\Domain\Interfaces\DomainEventBusInterface;

class LaravelDomainEventBus implements DomainEventBusInterface
{
    public function dispatch(object ...$events): void
    {
        foreach ($events as $event) {
            event($event);
        }
    }
}