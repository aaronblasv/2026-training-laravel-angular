<?php

declare(strict_types=1);

namespace App\Sale\Infrastructure\Entrypoint\Http;

use App\Sale\Application\GetAllSales\GetAllSales;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllSalesController
{
    public function __construct(
        private GetAllSales $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        $sales = ($this->useCase)(
            $request->user()->restaurant_id,
            is_string($from) ? $from : null,
            is_string($to)   ? $to   : null,
        );

        return new JsonResponse(
            array_map(fn($s) => [
                'uuid' => $s->uuid,
                'ticket_number' => $s->ticketNumber,
                'value_date' => $s->valueDate,
                'subtotal' => $s->subtotal,
                'tax_amount' => $s->taxAmount,
                'line_discount_total' => $s->lineDiscountTotal,
                'order_discount_total' => $s->orderDiscountTotal,
                'total' => $s->total,
                'refunded_total' => $s->refundedTotal,
                'net_total' => $s->netTotal,
                'table_name' => $s->tableName,
                'open_user_name' => $s->openUserName,
                'close_user_name' => $s->closeUserName,
                'opened_at' => $s->openedAt,
                'closed_at' => $s->closedAt,
            ], $sales)
        );
    }
}
