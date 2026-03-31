<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\DeactivateProduct\DeactivateProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeactivateProductController
{
    public function __construct(
        private DeactivateProduct $useCase,
    ) {}

    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        ($this->useCase)($uuid);
        return new JsonResponse(null, 204);
    }
}