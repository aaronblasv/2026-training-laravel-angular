<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\UpdateUserPhoto\UpdateUserPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateUserPhotoController
{
    public function __construct(
        private UpdateUserPhoto $updateUserPhoto,
    ) {}

    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'image_src' => 'nullable|string',
        ]);

        $response = ($this->updateUserPhoto)(
            $uuid,
            $request->user()->restaurant_id,
            $validated['image_src'] ?? null,
        );

        return new JsonResponse($response->toArray());
    }
}
