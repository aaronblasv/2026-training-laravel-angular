<?php

declare(strict_types=1);

namespace App\CashShift\Application\OpenCashShift;

use App\CashShift\Domain\Entity\CashShift;
use App\CashShift\Domain\Exception\CashShiftAlreadyOpenException;
use App\CashShift\Domain\Interfaces\CashShiftRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;

class OpenCashShift
{
    public function __construct(private CashShiftRepositoryInterface $repository) {}

    public function __invoke(int $restaurantId, string $userUuid, int $openingCash, ?string $notes): array
    {
        if ($this->repository->findOpenByRestaurant($restaurantId)) {
            throw new CashShiftAlreadyOpenException();
        }

        $cashShift = CashShift::open(
            Uuid::generate(),
            $restaurantId,
            Uuid::create($userUuid),
            $openingCash,
            $notes,
        );

        $this->repository->save($cashShift);

        return [
            'uuid' => $cashShift->uuid()->getValue(),
            'status' => $cashShift->status(),
            'opening_cash' => $cashShift->openingCash(),
            'opened_at' => $cashShift->openedAt()->format('Y-m-d H:i:s'),
        ];
    }
}