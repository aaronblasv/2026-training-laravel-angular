<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Domain\Interfaces\UserRepositoryInterface;
use App\User\Application\LogoutUser\LogoutUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController
{
    public function __construct(
        private LogoutUser $logoutUser,
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $uuid = $request->user()->uuid;

        $user = $this->userRepository->findById($uuid);

        $response = ($this->logoutUser)($user);

        return new JsonResponse(null, 204);
    }
}