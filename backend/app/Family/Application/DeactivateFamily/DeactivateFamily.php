<?php

namespace App\Family\Application\DeactivateFamily;

use App\Family\Domain\Interfaces\FamilyRepositoryInterface;

class DeactivateFamily
{
    public function __construct(
        private FamilyRepositoryInterface $repository,
    ) {}

    public function __invoke(string $uuid): void
    {
        $family = $this->repository->findById($uuid);

        if ($family === null) {
            throw new \Exception('Family not found');
        }

        $family->dddUpdate($family->getName(), false);

        $this->repository->save($family);
    }
}