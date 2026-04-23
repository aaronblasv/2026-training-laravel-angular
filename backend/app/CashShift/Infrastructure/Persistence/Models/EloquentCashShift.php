<?php

declare(strict_types=1);

namespace App\CashShift\Infrastructure\Persistence\Models;

use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'version',
    ];

    public function openedByUser(): BelongsTo
    {
        return $this->belongsTo(EloquentUser::class, 'opened_by_user_id')->withTrashed();
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(EloquentUser::class, 'closed_by_user_id')->withTrashed();
    }
}