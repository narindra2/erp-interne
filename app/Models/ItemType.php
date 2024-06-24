<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'category_id',
        'sub_category',
        'deleted'
    ];
    const IMMOBILISATION = "immobilisation";
    const CONSOMABLE = "consomable";
    
    public function category() {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }
    public static function getSubCategory() {
            return [self::IMMOBILISATION , self::CONSOMABLE];
    }   
}
