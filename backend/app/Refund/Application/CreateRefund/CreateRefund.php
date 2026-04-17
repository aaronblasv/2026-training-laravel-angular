<?php

declare(strict_types=1);

namespace App\Refund\Application\CreateRefund;

use App\Refund\Domain\Entity\Refund;
use App\Refund\Domain\Entity\RefundLine;
use App\Refund\Domain\Exception\RefundExceedsAvailableQuantityException;
use App\Refund\Domain\Interfaces\RefundRepositoryInterface;
use App\Sale\Domain\Exception\SaleNotFoundException;
use App\Sale\Domain\Interfaces\SaleRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;

class CreateRefund
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private RefundRepositoryInterface $refundRepository,
    ) {}

    public function __invoke(
        string $saleUuid,
        string $userUuid,
        string $method,
        ?string $reason,
        bool $refundAll,
        array $requestedLines,
        int $restaurantId,
    ): array {
        $sale = $this->saleRepository->findByUuid($restaurantId, $saleUuid);
        if (!$sale) {
            throw new SaleNotFoundException($saleUuid);
        }

        $lines = $this->saleRepository->findDomainLinesBySaleUuid($restaurantId, $saleUuid);
        $byUuid = [];
        foreach ($lines as $line) {
            $byUuid[$line->uuid()->getValue()] = $line;
        }

        $refundItems = [];

        if ($refundAll) {
            foreach ($lines as $line) {
                $availableQuantity = $line->availableQuantity();
                if ($availableQuantity <= 0) {
                    continue;
                }

                $refundItems[] = [$line, $availableQuantity];
            }
        } else {
            foreach ($requestedLines as $requestedLine) {
                $lineUuid = (string) ($requestedLine['sale_line_uuid'] ?? '');
                $quantity = (int) ($requestedLine['quantity'] ?? 0);
                $line = $byUuid[$lineUuid] ?? null;

                if (!$line || $quantity <= 0) {
                    continue;
                }

                if ($quantity > $line->availableQuantity()) {
                    throw new RefundExceedsAvailableQuantityException($lineUuid);
                }

                $refundItems[] = [$line, $quantity];
            }
        }

        $subtotal = 0;
        $taxAmount = 0;
        $total = 0;
        $refundLinePayload = [];

        foreach ($refundItems as [$line, $quantity]) {
            $lineSubtotal = (int) round($line->lineSubtotal() * ($quantity / $line->quantity()));
            $lineTax = (int) round($line->taxAmount() * ($quantity / $line->quantity()));
            $lineTotal = (int) round($line->lineTotal() * ($quantity / $line->quantity()));

            $subtotal += $lineSubtotal;
            $taxAmount += $lineTax;
            $total += $lineTotal;

            $refundLinePayload[] = [$line, $quantity, $lineSubtotal, $lineTax, $lineTotal];
        }

        $refund = Refund::dddCreate(
            Uuid::generate(),
            $restaurantId,
            $sale->uuid(),
            Uuid::create($userUuid),
            $refundAll ? 'full' : 'partial',
            $method,
            $reason,
            $subtotal,
            $taxAmount,
            $total,
        );

        $this->refundRepository->save($refund);

        foreach ($refundLinePayload as [$line, $quantity, $lineSubtotal, $lineTax, $lineTotal]) {
            $line->registerRefund($quantity);
            $this->saleRepository->updateLine($line);

            $refundLine = RefundLine::dddCreate(
                Uuid::generate(),
                $refund->uuid(),
                $line->uuid(),
                $quantity,
                $lineSubtotal,
                $lineTax,
                $lineTotal,
            );

            $this->refundRepository->saveLine($refundLine);
        }

        $sale->registerRefund($total);
        $this->saleRepository->update($sale);

        return [
            'uuid' => $refund->uuid()->getValue(),
            'type' => $refund->type(),
            'method' => $refund->method(),
            'subtotal' => $refund->subtotal(),
            'tax_amount' => $refund->taxAmount(),
            'total' => $refund->total(),
        ];
    }
}