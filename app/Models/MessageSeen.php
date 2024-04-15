<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageSeen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function message() {
        return $this->belongsTo(Message::class);
    }

    public static function getUserWhoSawMessage(Message $message) {
        return MessageSeen::with('user')
            ->whereDeleted(false)
            ->where('message_id', $message->id) 
            ->orderBy('created_at')
            ->get();
    }
}
