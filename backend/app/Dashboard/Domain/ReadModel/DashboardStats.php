<?php

declare(strict_types=1);

namespace App\Dashboard\Domain\ReadModel;

final readonly class DashboardStats
{
    public function __construct(
        public int $products,
        public int $families,
        public int $taxes,
        public int $users,
        public int $salesThisMonth,
        public int $revenueThisMonth,
    ) {}

    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'products'           => $this->products,
            'families'           => $this->families,
            'taxes'              => $this->taxes,
            'users'              => $this->users,
            'sales_this_month'   => $this->salesThisMonth,
            'revenue_this_month' => $this->revenueThisMonth,
        ];
    }
}
