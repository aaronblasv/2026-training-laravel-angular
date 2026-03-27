<?php
namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\UpdateProduct\UpdateProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateProductController
{
    public function __construct(private UpdateProduct $useCase) {}

    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        $product = ($this->useCase)(
            $uuid,
            $request->input('name'),
            $request->input('price'),
            $request->input('stock'),
            $request->input('active'),
            $request->input('family_id'),
            $request->input('tax_id'),
        );

        return new JsonResponse($product);
    }
}