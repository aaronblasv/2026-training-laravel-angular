<?php
namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\CreateProduct\CreateProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateProductController
{
    public function __construct(private CreateProduct $useCase) {}

    public function __invoke(Request $request): JsonResponse
    {
        $product = ($this->useCase)(
            $request->input('name'),
            $request->input('price'),
            $request->input('stock'),
            $request->input('active', true),
            $request->input('family_id'),
            $request->input('tax_id'),
        );

        return new JsonResponse($product, 201);
    }
}