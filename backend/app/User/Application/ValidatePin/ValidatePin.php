<?php

declare(strict_types=1);

namespace App\User\Application\ValidatePin;

use App\User\Domain\Interfaces\UserRepositoryInterface;

class ValidatePin
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    public function __invoke(string $pin): ValidatePinResponse
    {
        $user = $this->repository->findByPin($pin);
        if (!$user) {
            throw new \DomainException('Invalid PIN.');
        }

        return ValidatePinResponse::create($user);
    }
}