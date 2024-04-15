<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmationCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'users_id',
        'code',
        'expiration_code'
    ];

    public static function generate($userId)
    {
        $conf = new ConfirmationCode();
        $conf->users_id = $userId;
        $conf->code = rand(1000, 100000);
        $conf->expiration_date = Carbon::now()->addDays(1);
        $conf->save();
        return $conf;
    }

    public static function check($registration_number, $code)
    {
        $user = User::where('registration_number', $registration_number)->first();
        if ($user == null) {
            throw new Exception('Erreur de matricule');
        }
        $confirmationCode = ConfirmationCode::where('users_id', $user->id)->where('code', $code)->first();
        if ($confirmationCode == null) {
            throw new Exception('Code incorrect');
        }
    }
}