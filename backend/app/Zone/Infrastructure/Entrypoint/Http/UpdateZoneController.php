<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\UpdateZone\UpdateZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateZoneController
{
    public function __construct(
        private UpdateZone $useCase,
    ) {}

    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $zone = ($this->useCase)($uuid, $validated['name'], $request->user()->restaurant_id);

        return new JsonResponse($zone);
    }
}
