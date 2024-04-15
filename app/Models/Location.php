<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public static $_ID_STOCK = 1;

    public function getLocal() {
        if ($this->local === null)  return null;
        return "Local " . $this->local;
    }
}
