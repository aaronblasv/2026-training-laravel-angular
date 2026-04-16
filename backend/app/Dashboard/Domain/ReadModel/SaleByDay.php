<?php

declare(strict_types=1);

namespace App\Dashboard\Domain\ReadModel;

final readonly class SaleByDay
{
    public function __construct(
        public string $day,
        public int    $count,
        public int    $total,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'day'   => $this->day,
            'count' => $this->count,
            'total' => $this->total,
        ];
    }
}
