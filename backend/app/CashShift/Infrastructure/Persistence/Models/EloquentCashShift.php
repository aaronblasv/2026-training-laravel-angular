<?php

declare(strict_types=1);

namespace App\CashShift\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentCashShift extends Model
{
    protected $table = 'cash_shifts';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'opened_by_user_id',
        'closed_by_user_id',
        'status',
        'opening_cash',
        'cash_total',
        'card_total',
        'bizum_total',
        'refund_total',
        'counted_cash',
        'cash_difference',
        'notes',
        'opened_at',
        'closed_at',
    ];
}