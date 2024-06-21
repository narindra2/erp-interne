<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type_id',
        'purchase_id',
        'num_invoice_id',
        'price_htt',
        'price_ht',
        'propriety',
        'observation',
        'date',
        'deleted',
        'code'
    ];

    const SEPARATOR_CODE = "/";

    public function article() {
        return $this->belongsTo(ItemType::class, "item_type_id");
    }
    public function purchase() {
        return $this->belongsTo(Purchase::class, "purchase_id");
    }
    public function num_invoice() {
        return $this->belongsTo(Purchase::class, "num_invoice_id");
    }
}
