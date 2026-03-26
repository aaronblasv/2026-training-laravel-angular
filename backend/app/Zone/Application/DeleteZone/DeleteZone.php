<?php

namespace App\Zone\Application\DeleteZone;

use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;

class DeleteZone
{

    private ZoneRepositoryInterface $repository;

    public function __construct(ZoneRepositoryInterface $repository) 
    {
        $this->repository = $repository;
    }

    public function __invoke(string $uuid): void 
    {
        
        $zone = $this->repository->findById($uuid);

        if(!$zone) {
            throw new \Exception('Zone not found');
        }

        $this->repository->delete($uuid);
        
    }
}