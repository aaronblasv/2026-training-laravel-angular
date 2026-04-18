<?php

declare(strict_types=1);

namespace App\Sale\Domain\Interfaces;

use App\Sale\Domain\Entity\Sale;
use App\Sale\Domain\Entity\SaleLine;
use App\Sale\Domain\ReadModel\SaleLineDetail;
use App\Sale\Domain\ReadModel\SalesGroupedReport;
use App\Sale\Domain\ReadModel\SaleSummary;

interface SaleRepositoryInterface
{
    public function save(Sale $sale): void;
    public function saveLine(SaleLine $line): void;
    public function update(Sale $sale): void;
    public function updateLine(SaleLine $line): void;
    public function getNextTicketNumber(int $restaurantId): int;
    public function findAll(int $restaurantId): array;
    public function findByUuid(int $restaurantId, string $saleUuid): ?Sale;
    public function findDomainLinesBySaleUuid(int $restaurantId, string $saleUuid): array;

    /** @return SaleSummary[] */
    public function findFiltered(int $restaurantId, ?string $from, ?string $to): array;

    /** @return SaleLineDetail[] */
    public function findLinesBySaleUuid(int $restaurantId, string $saleUuid): array;

    public function getGroupedReport(int $restaurantId, ?string $from, ?string $to): SalesGroupedReport;
}