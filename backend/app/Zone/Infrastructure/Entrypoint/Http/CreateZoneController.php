<?php

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
        $name = $request->input('name');
        $zone = ($this->useCase)($name);

        return new JsonResponse($zone);
    }
}