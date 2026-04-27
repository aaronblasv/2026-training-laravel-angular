<?php

declare(strict_types=1);

namespace App\User\Application\UpdateUserPhoto;

use App\User\Application\UpdateUser\UpdateUserResponse;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Interfaces\UserRepositoryInterface;

class UpdateUserPhoto
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(string $uuid, int $restaurantId, ?string $imageSrc = null): UpdateUserResponse
    {
        $user = $this->userRepository->findById($uuid, $restaurantId);

        if ($user === null) {
            throw new UserNotFoundException($uuid);
        }

        $user->updatePhoto($imageSrc);
        $this->userRepository->save($user);

        return UpdateUserResponse::create($user);
    }
}