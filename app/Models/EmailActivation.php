<?php

namespace App\Models;

use DateTime;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EmailActivation extends Model
{
    use HasFactory;
    protected $table = "email_activation";
    protected $fillable = ['token', 'expiration_date', 'users_id', 'deleted'];

    public function generateTokenAndSave($users_id) {
        $this->attributes['users_id'] = $users_id;
        $datetime = new DateTime();
        $this->attributes['token'] = Hash::make($this->attributes['users_id'] . $datetime->format('U'));
        $this->attributes['token'] = str_replace("/", "", $this->attributes['token']);
        $this->attributes['expiration_date'] = $datetime->add(new DateInterval('P1D'));
        $this->save();
    }
}
