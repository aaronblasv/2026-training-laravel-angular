<?php

declare(strict_types=1);

namespace App\Shared\Domain\Support;

trait RecordsDomainEvents
{
    /** @var array<object> */
    private array $domainEvents = [];

    public function recordDomainEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return array<object> */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}