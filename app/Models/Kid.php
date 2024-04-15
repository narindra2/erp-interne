<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kid extends Model
{
    use HasFactory;
    protected $fillable = ['fullname','birthdate','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
