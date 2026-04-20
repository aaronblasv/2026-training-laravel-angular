<?php

declare(strict_types=1);

namespace App\Shared\Domain\Interfaces;

interface DomainEventBusInterface
{
    public function dispatch(object ...$events): void;
}