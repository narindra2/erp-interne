<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointingTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'minute_worked'
    ];
    public function user() {
        return $this->hasOne(User::class, "user_id");
    }

    public static function saveOrUpdatePointingTemp($input) {
        PointingTemp::updateOrCreate([
            'user_id' => $input['user_id']
        ], $input);
    }

    public static function createFilter() {
        $filters = [];
        $filters[] = [
            "label" => "local",
            "name" => "local",
            "type" => "select",
            "options" => [
                [
                    "text" => "Local 1",
                    "value" => "1"
                ],
                [
                    "text" => "Local 2",
                    "value" => "2"
                ],
            ]
        ];
        return $filters;
    }
}
