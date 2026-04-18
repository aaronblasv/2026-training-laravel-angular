<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Persistence\Models;

use App\Table\Infrastructure\Persistence\Models\EloquentTable;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentOrder extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'status',
        'table_id',
        'opened_by_user_id',
        'closed_by_user_id',
        'diners',
        'discount_type',
        'discount_value',
        'discount_amount',
        'opened_at',
        'closed_at',
    ];

    public function lines()
    {
        return $this->hasMany(EloquentOrderLine::class, 'order_id');
    }

    public function table()
    {
        return $this->belongsTo(EloquentTable::class, 'table_id');
    }

    public function openedByUser()
    {
        return $this->belongsTo(EloquentUser::class, 'opened_by_user_id');
    }

    public function closedByUser()
    {
        return $this->belongsTo(EloquentUser::class, 'closed_by_user_id');
    }
}