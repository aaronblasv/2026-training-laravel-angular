<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\GetAuthenticatedUser\GetAuthenticatedUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAuthenticatedUserController
{
    public function __construct(
        private GetAuthenticatedUser $getAuthenticatedUser,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $response = ($this->getAuthenticatedUser)($user->uuid);

        $restaurantName = \DB::table('restaurants')
            ->where('id', $user->restaurant_id)
            ->value('name');

        return new JsonResponse(array_merge($response->toArray(), [
            'restaurant_name' => $restaurantName,
        ]));
    }
}