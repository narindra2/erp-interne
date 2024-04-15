<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReactionMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message_id',
        'message_reaction_id'
    ];

    public static function saveUserReaction($input, $user, &$addition) {
        $userReactionMessage = UserReactionMessage::where('message_id', $input['message_id'])
            ->where('user_id', $user->id)
            ->where('message_reaction_id', $input['message_reaction_id'])
            ->whereDeleted(false)
            ->first();
        if ($userReactionMessage) {
            $userReactionMessage->deleted = true;
            $userReactionMessage->save();
            $addition = -1;
        } else {
            $input['user_id'] = $user->id;
            $userReactionMessage = UserReactionMessage::create($input);
        }
        return $userReactionMessage;
    }
}
