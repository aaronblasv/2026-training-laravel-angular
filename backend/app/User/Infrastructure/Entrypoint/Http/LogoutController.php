<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\LogoutUser\LogoutUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController
{
    public function __construct(
        private LogoutUser $logoutUser,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        ($this->logoutUser)($request->user()->uuid);

        return new JsonResponse(null, 204);
    }
}