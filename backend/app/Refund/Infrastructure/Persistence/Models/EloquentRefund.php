<?php

declare(strict_types=1);

namespace App\Refund\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentRefund extends Model
{
    protected $table = 'refunds';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'sale_id',
        'user_id',
        'type',
        'method',
        'reason',
        'subtotal',
        'tax_amount',
        'total',
    ];
}