<?php

namespace App\Family\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Family\Infrastructure\Persistence\Models\EloquentFamily;
use App\Tax\Infrastructure\Persistence\Models\EloquentTax;


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