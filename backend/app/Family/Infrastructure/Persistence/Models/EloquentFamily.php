<?php

declare(strict_types=1);

namespace App\Family\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EloquentFamily extends Model
{

    use SoftDeletes;

    protected $table = 'families';

    protected $fillable = [
        'uuid',
        'name',
        'active',
        'restaurant_id',
        'tax_id',
    ];

}