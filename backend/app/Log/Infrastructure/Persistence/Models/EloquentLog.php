<?php

namespace App\Log\Infrastructure\Persistence\Models;

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
}
