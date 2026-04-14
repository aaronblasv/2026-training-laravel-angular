<?php

namespace App\Payment\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentPayment extends Model
{
    protected $table = 'payments';
    protected $fillable = ['uuid', 'order_id', 'user_id', 'amount', 'method', 'description'];
    public $timestamps = true;
}
