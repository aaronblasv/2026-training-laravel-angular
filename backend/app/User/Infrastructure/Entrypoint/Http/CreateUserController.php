<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\CreateUser\CreateUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateUserController
{
    public function __construct(
        private CreateUser $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = ($this->useCase)(
            $request->input('email'),
            $request->input('name'),
            $request->input('password'),
            $request->input('role', 'waiter'),
            auth()->user()->restaurant_id,
        );

        return new JsonResponse($user, 201);
    }
}