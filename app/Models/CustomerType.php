<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    use HasFactory;

    protected $table = "customer_type";

    public function customers(){
        return $this->hasMany(Customer::class);
    }

    public static function dropdown()
    {
        $list = [];
        $customerTypes = CustomerType::whereDeleted(0)->get();
        foreach ($customerTypes as $customerType) {
            $list[] = ["value" => $customerType->id , "text" => $customerType->name ];
        }
        return $list;
    }

}
