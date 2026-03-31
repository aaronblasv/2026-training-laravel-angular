<?php

namespace App\User\Application\GetAuthenticatedUser;

use App\User\Domain\Interfaces\UserRepositoryInterface;
use App\User\Application\GetAuthenticatedUser\GetAuthenticatedUserResponse;

class GetAuthenticatedUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(string $uuid): GetAuthenticatedUserResponse
    {
        $user = $this->userRepository->findById($uuid);

        if($user === null) {
            throw new \Exception('User not found');
        }

        return GetAuthenticatedUserResponse::create($user);
    }
}