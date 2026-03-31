<?php

namespace App\Family\Application\ActivateFamily;

use App\Family\Domain\Interfaces\FamilyRepositoryInterface;

class ActivateFamily
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

        $family->dddUpdate($family->getName(), true);

        $this->repository->save($family);
    }
}