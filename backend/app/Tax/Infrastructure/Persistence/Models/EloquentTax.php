<?php

namespace App\Tax\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentTax extends Model
{

    use SoftDeletes;

    protected $table = 'taxes';

    protected $fillable = [
        'uuid',
        'name',
        'percentage'
    ];

}