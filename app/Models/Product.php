<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'min_quantity',
        'categories_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function mvt()
    {
        return $this->hasMany(MvtProduct::class, 'products_id');
    }

    public static function saveOrUpdate($input)
    {
        $id = null;
        if (isset($input['id']))    $id = $input['id'];
        return Product::updateOrCreate([
            'id' => $id
        ], [
            'categories_id' => $input['categories_id'],
            'min_quantity' => $input['min_quantity'],
            'name' => $input['name']
        ]);

        // return MvtProduct::updateOrCreate([
        //     'id' => $id
        // ], [
        //     'products_id' => $input['products_id'],
        //     'input_quantity' => $input['quantity'],
        //     'unit_price' => $input['unit_price'],
        //     'mvt_date' => $input['mvt_date'],
        //     'users_id' => auth()->user()->id
        // ]);
    }

    public function getStateAttribute()
    {
        if ($this->stock_quantity > $this->min_quantity)     return "OK";
        if ($this->stock_quantity <= 0)                      return "Rupture";
        if ($this->stock_quantity <= $this->min_quantity)    return "Avertissement";
    }
}
