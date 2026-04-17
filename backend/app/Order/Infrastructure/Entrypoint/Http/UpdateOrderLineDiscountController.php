<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Log\Application\CreateLog\CreateLog;
use App\Order\Application\UpdateOrderLineDiscount\UpdateOrderLineDiscount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateOrderLineDiscountController
{
    public function __construct(
        private UpdateOrderLineDiscount $useCase,
        private CreateLog $createLog,
    ) {}

    public function __invoke(Request $request, string $orderUuid, string $lineUuid): JsonResponse
    {
        $validated = $request->validate([
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_value' => 'nullable|integer|min:0',
        ]);

        $discountType = $validated['discount_type'] ?? null;
        $discountValue = (int) ($validated['discount_value'] ?? 0);

        ($this->useCase)(
            $lineUuid,
            $discountType,
            $discountValue,
            $request->user()->restaurant_id,
        );

        ($this->createLog)(
            $request->user()->restaurant_id,
            $request->user()->uuid,
            'order.line.discount.updated',
            'order_line',
            $lineUuid,
            [
                'order_uuid' => $orderUuid,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
            ],
            $request->ip(),
        );

        return new JsonResponse(null, 204);
    }
}