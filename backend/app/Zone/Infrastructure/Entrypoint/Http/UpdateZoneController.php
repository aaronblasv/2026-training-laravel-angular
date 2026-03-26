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
        $name = $request->input('name');

        $zone = ($this->useCase)($uuid, $name);

        return new JsonResponse($zone);
    }
}