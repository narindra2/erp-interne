<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageGroupParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message_group_id'
    ];

    public function messageGroup() {
        return $this->belongsTo(MessageGroup::class, 'message_group_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getUserGroup(User $user) {
        return MessageGroupParticipant::with('messageGroup')->where('user_id', $user->id)->get();
    }

    public static function addUserToGroup($input) {
        $messageGroupParticipant = MessageGroupParticipant::whereDeleted(false)->where('message_group_id', $input['message_group_id'])->where('user_id', $input['user_id'])->first();
        if (!$messageGroupParticipant) {
            $messageGroupParticipant = MessageGroupParticipant::create($input);
        }
        return $messageGroupParticipant ;
    }

    public static function checkUserIsInTheGroup(MessageGroup $messageGroup, User $user) {
        $count = MessageGroupParticipant::where('user_id', $user->id)->where('message_group_id', $messageGroup->id)->whereDeleted(false)->count();
        return ($count > 0) ? true : false;
    }
}
