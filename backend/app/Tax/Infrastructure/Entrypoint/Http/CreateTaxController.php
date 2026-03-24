<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\CreateTax\CreateTax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateTaxController
{
    public function __construct(
        private CreateTax $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $name = $request->input('name');
        $percentage = $request->input('percentage');

        $tax = ($this->useCase)($name, $percentage);

        return new JsonResponse($tax);
    }
}