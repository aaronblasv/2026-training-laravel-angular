<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\GetAllZones\GetAllZones;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllZonesController
{
    public function __construct(
        private GetAllZones $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $zones = ($this->useCase)($request->user()->restaurant_id);

        return new JsonResponse($zones);
    }
}
