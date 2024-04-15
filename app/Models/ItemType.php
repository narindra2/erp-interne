<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'unit_id',
        'item_category_id',
        'unit_price',
    ];

    public function unit() {
        return $this->belongsTo(UnitItem::class, 'unit_id');
    }

    public function items() {
        return $this->hasMany(Item::class, "item_type_id");
    }

    public function mvts() {
        return $this->hasManyThrough(ItemMovement::class, Item::class, 'item_type_id', 'item_id');
    }

    public function countItem($itemMovements, $status=null) {
        if (!$status)   return $this->items->count();
        $mvts = clone $itemMovements;
        return $mvts->where('item_status_id', $status)->where('is_actual', true)->count();
    }
}
