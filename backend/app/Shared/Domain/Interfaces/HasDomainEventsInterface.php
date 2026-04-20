<?php

declare(strict_types=1);

namespace App\Shared\Domain\Interfaces;

interface HasDomainEventsInterface
{
    public function recordDomainEvent(object $event): void;

    /** @return array<object> */
    public function pullDomainEvents(): array;
}