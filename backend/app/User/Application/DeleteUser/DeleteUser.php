<?php

namespace App\User\Application\DeleteUser;

use App\User\Domain\Interfaces\UserRepositoryInterface;
use App\User\Domain\Entity\User;

class DeleteUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(string $uuid): void
    {
        $user = $this->userRepository->findById($uuid);

        if($user === null) {
            throw new \Exception('User not found');
        }

        $this->userRepository->delete($user);
    }
}