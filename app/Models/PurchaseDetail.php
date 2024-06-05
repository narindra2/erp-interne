<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type_id',
        'purchase_id',
        'quantity',
        'unit_price',
        'unit_item_id',
        'propriety',
        'deleted'
    ];

    public function unit() {
        return $this->belongsTo(UnitItem::class, 'unit_item_id');
    }

    public function itemType() {
        return $this->belongsTo(ItemType::class, "item_type_id");
    }

    public function getTotalPriceAttribute() {
        return $this->quantity * $this->unit_price;
    }
}
