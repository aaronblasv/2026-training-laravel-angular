<?php

declare(strict_types=1);

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\CreateZone\CreateZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateZoneController
{
    public function __construct(
        private CreateZone $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $zone = ($this->useCase)($validated['name'], $request->user()->restaurant_id);

        return new JsonResponse($zone->toArray(), 201);
    }
}
