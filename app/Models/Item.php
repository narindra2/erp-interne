<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'item_type_id'
    ];

    public function type() {
        return $this->belongsTo(ItemType::class, "item_type_id");
    }

    public function mvts() {
        return $this->hasMany(ItemMovement::class, "item_id");
    }

    public function lastMvt() {
        return $this->hasOne(ItemMovement::class, "item_id")->latestOfMany();
    }

    public function getNameAndCode() {
        return $this->code . " - " . $this->type->name;
    }
}
