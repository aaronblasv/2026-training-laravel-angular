<?php

namespace App\Product\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EloquentProduct extends Model
{

    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'uuid',
        'family_id',
        'tax_id',
        'image_src',
        'name',
        'price',
        'stock',
        'active'
    ];

}