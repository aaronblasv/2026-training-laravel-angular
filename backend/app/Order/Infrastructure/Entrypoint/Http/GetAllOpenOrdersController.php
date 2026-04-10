<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\GetAllOpenOrders\GetAllOpenOrders;
use Illuminate\Http\JsonResponse;

class GetAllOpenOrdersController
{
    public function __construct(private GetAllOpenOrders $useCase) {}

    public function __invoke(): JsonResponse
    {
        return new JsonResponse(($this->useCase)());
    }
}