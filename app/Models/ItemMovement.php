<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'place',
        'item_id',
        'user_id'
    ];
    public function item() {
        return $this->belongsTo(Item::class, "item_id");
    }
    public function location() {
        return $this->belongsTo(Location::class, "location_id");
    }
}
