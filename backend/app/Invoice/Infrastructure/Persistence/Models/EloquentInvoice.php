<?php

namespace App\Invoice\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentInvoice extends Model
{
    protected $table = 'invoices';
    protected $fillable = ['uuid', 'order_id', 'invoice_number', 'subtotal', 'tax_amount', 'total', 'issued_at'];
    public $timestamps = true;
}
