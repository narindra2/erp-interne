<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function participants() {
        return $this->hasMany(MessageGroupParticipant::class, 'message_group_id');
    }

    public function getFirstLetter() {
        return strtoupper(substr($this->name, 0, 1));
    }

    public static function saveOrUpdateMessageGroup($input, User $user, &$message) {
        $id = get_array_value($input, 'id');
        $messageGroup = MessageGroup::updateOrCreate(['id' => $id], $input);
        if (!$id) {
            $messageGroupParticipant = new MessageGroupParticipant();
            $messageGroupParticipant->message_group_id = $messageGroup->id;
            $messageGroupParticipant->user_id = $user->id;
            $messageGroupParticipant->save();
            $message = "Le groupe " . $messageGroup->name . ' a été créé avec succès.';
        }
        return $messageGroup;
    }

    public function getIdParticipants() {
        return MessageGroupParticipant::select('user_id')
            ->where("message_group_id", $this->id)->whereDeleted(0)->get()
            ->pluck('user_id')->unique()->toArray();
    }

    public function getParticipants() {
        return MessageGroupParticipant::select('user_id')
            ->where("message_group_id", $this->id)->whereDeleted(0)->get();
    }
}
