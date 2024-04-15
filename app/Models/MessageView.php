<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageView extends Message
{
    use HasFactory;

    protected $table = "v_messages";

    //Count 
    public static function getMessagesNotSeen(User $user) {
        return MessageView::with(['sender' => function($query) {
            $query->withOut('userJob');
        }])
            ->selectRaw('COUNT(*) AS count, sender_id')
            ->where('receiver_id', $user->id)
            ->whereNull('message_seens_id')
            ->whereDeleted(0)
            ->groupBy('sender_id')
            ->get();
    }

    public static function getMessagesNotSeenGroup(User $user) {
        // delete_cache("getMessagesNotSeenGroup_$user->id");
        $messageIds = MessageSeen::select("message_id")->whereDeleted(0)->where("user_id", $user->id)->get()->toArray();
        $groupIds = MessageGroup::select('id')->whereHas("participants", function ($query) use ($user) {
            $query->where('user_id', $user->id);
            $query->whereDeleted(false);
        })->get();
        return Message::with(['group'])
            ->selectRaw("COUNT(*) AS count, group_id")
            ->whereIn("group_id", $groupIds)
            ->where('sender_id', "<>", $user->id)
            ->whereNotIn("id", $messageIds)
            ->groupBy("group_id")
            ->get();
    }

    public static function getMessagesNotSeenInDiscussion(User $sender, User $receiver) {
        return MessageView::whereNull('message_seens_created_at')
            ->where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->whereDeleted(false)
            ->get();
    }

    public static function makeMessagesRead(User $sender, User $receiver) {
        $messagesNotSeen = MessageView::getMessagesNotSeenInDiscussion($sender, $receiver);
        $dataToSave= [];
        foreach ($messagesNotSeen as $message) {
            $dataToSave[] = [
                'user_id' => $receiver->id,
                'message_id' => $message->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        MessageSeen::insert($dataToSave);
        if ($messagesNotSeen->count() > 0) {
            delete_cache("getMessagesNotSeen_" . $receiver->id);
        }
    }

    public static function makeMessagesReadGroup(MessageGroup $messageGroup, User $user) {
        $messageIds = MessageSeen::select("message_id")->whereDeleted(0)->where("user_id", $user->id)->get()->toArray();
        $messagesNotSeenInGroup = Message::whereNotIn("id", $messageIds)
            ->whereNotNull("group_id")
            ->get();
        $dataToSave = [];
        foreach ($messagesNotSeenInGroup as $message) {
            $dataToSave[] = [
                'user_id' => $user->id,
                'message_id' => $message->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        MessageSeen::insert($dataToSave);
        if ($messagesNotSeenInGroup->count() > 0) {
            delete_cache("getMessagesNotSeenGroup_" . $user->id);
        }
    }
}
