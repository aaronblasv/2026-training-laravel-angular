<?php

declare(strict_types=1);

namespace App\Dashboard\Domain\ReadModel;

final readonly class SaleThisMonth
{
    public function __construct(
        public string $uuid,
        public string $ticketNumber,
        public int    $total,
        public string $valueDate,
        public string $tableName,
        public string $userName,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uuid'          => $this->uuid,
            'ticket_number' => $this->ticketNumber,
            'total'         => $this->total,
            'value_date'    => $this->valueDate,
            'table_name'    => $this->tableName,
            'user_name'     => $this->userName,
        ];
    }
}
