<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\GetAllProducts\GetAllProducts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllProductsController
{
    public function __construct(
        private GetAllProducts $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse(($this->useCase)($request->user()->restaurant_id));
    }
}
