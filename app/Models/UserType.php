<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected $table = 'user_type';

    public static $_ADMIN = 1;
    public static $_HR = 2;
    public static $_TECH = 3;
    public static $_CONTRIBUTOR = 4;

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
