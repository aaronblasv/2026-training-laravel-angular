<?php

namespace App\Tax\Application\DeleteTax;

use App\Tax\Domain\Interfaces\TaxRepositoryInterface;

class DeleteTax
{

    private TaxRepositoryInterface $repository;

    public function __construct(TaxRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(string $uuid): void
    {

        $tax = $this->repository->findbyId($uuid);

        if(!$tax) {
            throw new \Exception('Tax not found');
        }

        $this->repository->delete($uuid);

    }

}