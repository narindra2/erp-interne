<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    use HasFactory;
    protected $table  ="meeting-rooms";
    protected $fillable = [
        'name',
        'creator_id',
        'deleted',
        'created_at',
        'updated_at'
    ];

    public static function get_available_room(){
        return MeetingRoom::whereDeleted(0)->get();
    }
}
