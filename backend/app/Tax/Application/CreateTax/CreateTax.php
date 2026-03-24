<?php

namespace App\Tax\Application\CreateTax;

use App\Tax\Domain\Entity\Tax;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;
use App\Tax\Application\CreateTax\CreateTaxResponse;
use App\Shared\Domain\ValueObject\Uuid;
use App\Tax\Domain\ValueObject\TaxName;
use App\Tax\Domain\ValueObject\TaxPercentage;

class CreateTax
{
    public function __construct(
        private TaxRepositoryInterface $repository,
    ) {}

    public function __invoke(string $name, int $percentage): CreateTaxResponse
    {
        $tax = Tax::dddCreate(
            Uuid::generate(),
            TaxName::create($name),
            TaxPercentage::create($percentage),
        );

        $this->repository->save($tax);

        return CreateTaxResponse::create($tax);
    }
}