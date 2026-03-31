<?php

namespace App\User\Application\GetAllUsers;

use App\User\Domain\Interfaces\UserRepositoryInterface;

class GetAllUsers
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(): array
    {
        $users = $this->userRepository->findAll();

        return array_map(
            fn($user) => GetAllUsersResponse::create($user),
            $users
        );
    }
}