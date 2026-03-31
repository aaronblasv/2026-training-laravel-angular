<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\ActivateFamily\ActivateFamily;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivateFamilyController
{
    public function __construct(
        private ActivateFamily $useCase,
    ) {}

    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        ($this->useCase)($uuid);
        return new JsonResponse(null, 204);
    }
}