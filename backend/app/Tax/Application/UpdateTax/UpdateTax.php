<?php

namespace App\Tax\Application\UpdateTax;

use App\Tax\Domain\Entity\Tax;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;
use App\Tax\Application\UpdateTax\UpdateTaxResponse;
use App\Shared\Domain\ValueObject\Uuid;
use App\Tax\Domain\ValueObject\TaxName;
use App\Tax\Domain\ValueObject\TaxPercentage;

class UpdateTax
{

    private TaxRepositoryInterface $repository;

    public function __construct(TaxRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(string $uuid, string $name, int $percentage): UpdateTaxResponse
    {

        $tax = $this->repository->findbyId($uuid);

        if(!$tax) {
            throw new \Exception('Tax not found');
        }

        $tax->dddUpdate(
            TaxName::create($name),
            TaxPercentage::create($percentage),
        );

        $this->repository->save($tax);

        return UpdateTaxResponse::create($tax);

    }

}