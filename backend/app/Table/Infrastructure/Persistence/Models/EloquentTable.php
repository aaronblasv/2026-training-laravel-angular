<?php

declare(strict_types=1);

namespace App\Table\Infrastructure\Persistence\Models;

use App\Zone\Infrastructure\Persistence\Models\EloquentZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class EloquentTable extends Model
{

    use SoftDeletes;

    protected $table = 'tables';

    protected $fillable = [
        'uuid',
        'name',
        'zone_id',
        'restaurant_id',
        'merged_with',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(EloquentZone::class, 'zone_id')->withTrashed();
    }

}