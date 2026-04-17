<?php

declare(strict_types=1);

namespace App\Refund\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentRefundLine extends Model
{
    protected $table = 'refund_lines';

    protected $fillable = [
        'uuid',
        'refund_id',
        'sale_line_id',
        'quantity',
        'subtotal',
        'tax_amount',
        'total',
    ];
}