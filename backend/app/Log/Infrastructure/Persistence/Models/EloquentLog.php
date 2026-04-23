<?php

declare(strict_types=1);

namespace App\Log\Infrastructure\Persistence\Models;

use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EloquentLog extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'user_id',
        'action',
        'entity_type',
        'entity_uuid',
        'data',
        'ip_address',
    ];

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(EloquentUser::class, 'user_id', 'uuid')->withTrashed();
    }
}
