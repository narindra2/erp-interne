<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseNumInvoiceLine extends Model
{
    use HasFactory;
    protected $table = "purchase_num_invoice";

    protected $fillable = [
        'purchase_id',
        'num_invoice',
        'user_id',
        'deleted',
    ];

}
