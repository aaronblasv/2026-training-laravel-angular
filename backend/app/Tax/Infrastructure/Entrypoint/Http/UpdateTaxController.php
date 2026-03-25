<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\UpdateTax\UpdateTax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateTaxController
{
    public function __construct(
        private UpdateTax $useCase,
    ) {}

    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        $name = $request->input('name');
        $percentage = $request->input('percentage');

        $tax = ($this->useCase)($uuid, $name, $percentage);

        return new JsonResponse($tax);
    }
}