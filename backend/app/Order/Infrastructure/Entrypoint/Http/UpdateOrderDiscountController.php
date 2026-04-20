<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\UpdateOrderDiscount\UpdateOrderDiscount;
use App\Shared\Application\Context\AuditContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateOrderDiscountController
{
    public function __construct(
        private UpdateOrderDiscount $useCase,
    ) {}

    public function __invoke(Request $request, string $orderUuid): JsonResponse
    {
        $validated = $request->validate([
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_value' => 'nullable|integer|min:0',
        ]);

        $discountType = $validated['discount_type'] ?? null;
        $discountValue = (int) ($validated['discount_value'] ?? 0);

        ($this->useCase)(
            new AuditContext(
                $request->user()->restaurant_id,
                $request->user()->uuid,
                $request->ip(),
            ),
            $orderUuid,
            $discountType,
            $discountValue,
        );

        return new JsonResponse(null, 204);
    }
}