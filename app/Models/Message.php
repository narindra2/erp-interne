<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // public $_my_message = true;

    protected $fillable = [
        'content',
        'sender_id',
        'receiver_id',
        'group_id',
        'is_file',
        'url',
        'created_at',
        'updated_at'
    ];

    public function sender() {
        return $this->belongsTo(User::class);
    }

    public function receiver() {
        return $this->belongsTo(User::class);
    }

    public function seen() {
        return $this->belongsTo(MessageSeen::class, 'message_id');
    }

    public function group() {
        return $this->belongsTo(MessageGroup::class);
    }

    public function reactions() {
        return $this->hasMany(UserReactionMessage::class, 'message_id');
    }
    
    public function getViewOfUserReaction() {
        $userReactions = $this->reactions;
        $data = [];
        foreach ($userReactions as $userReaction) {
            $exist = false;
            foreach ($data as $key => $count) {
                if ($key == $userReaction->message_reaction_id) {
                    $data[$key] += 1;
                    $exist = true;
                    break;
                }
            }
            if (!$exist) {
                $data[$userReaction->message_reaction_id] = 1;
            }
        }
        // dd($data);
        return view('messaging.modals.reactions.user-reaction', ['data' => $data, 'message' => $this])->render();
    }

    public static function getDiscussionGroup(MessageGroup $messageGroup, User $user, $offset=0, $pagination=10) {
        $messages = Message::with(['sender' => function($query) { $query->withOut('userJob'); },
            'reactions' => function($query) { $query->whereDeleted(false); }])
            ->where('group_id', $messageGroup->id)
            ->orderBy('created_at', 'DESC')
            ->offset($offset)
            ->limit($pagination)
            ->get();
        return $messages->reverse();
    }

    public static function getDiscussion(User $me, User $anotherUser, $offset=0, $pagination=10) {
        $messages = Message::with(['sender' => function($query) {
            $query->withOut('userJob');
        }, 'reactions' => function($query) {
            $query->whereDeleted(false);
        }])
            ->where(function($query) use ($me, $anotherUser){
                $query->where('sender_id', $me->id);
                $query->where('receiver_id', $anotherUser->id);
            })->orWhere(function($query) use ($me, $anotherUser) {
                $query->where('receiver_id', $me->id);
                $query->where('sender_id', $anotherUser->id);
            })->orderBy('created_at', 'DESC')->offset($offset)->limit($pagination)->get();
        return $messages->reverse();
    }

    public function getUserListWhoReact($messageReactionID) {
        $reactions = $this->reactions->where('message_reaction_id', $messageReactionID);
        return view('messaging.modals.reactions.user-list-who-react', ['reactions' => $reactions])->render();
    }

    public function getFileExtension() {
        $array = explode(".", $this->content);
        return array_key_last($array);
    }

    public function getLimitFilename() {
        return str_limite($this->content, 20, ".".  $this->getFileExtension());
    }

    public function getCssClasses() {
        if (!$this->isMyMessage()) return ["d-flex justify-content-start mb-3", "d-flex flex-column align-items-start message-class", "p-3 rounded bg-secondary text-dark fw-bold mw-lg-400px text-start"];
        return ["d-flex justify-content-end mb-3", "d-flex flex-column align-items-end message-class", "p-3 rounded bg-primary text-white mw-lg-400px text-end"];
    }

    /**
        * StoreMessage function contains four (04) parameters
        * 1- the user's input
        * 2- Files that users send
        * 3- the references of the user's list
        * 4- the reference of the messageGroup  
     */
    public static function storeMessage($input, $files, &$notifiables, &$messageGroup) {
        $input['sender_id'] = Auth::id();
        $content = get_array_value($input, 'content');
        $id = get_array_value($input, 'id');
        $messages = [];
        $input['created_at'] = Carbon::now();
        $input['updated_at'] = Carbon::now();
        if ($content) {
            $messages[] = Message::updateOrCreate(['id' => $id], $input);
        }
        //If user sends files, we loop each file and save it in to database and in the server
        if ($files != null) {
            foreach ($files as $file) {
                $input['created_at'] = $input['created_at']->addSecond();
                $input['updated_at'] = $input['updated_at']->addSecond();
                $messages[] = self::importFile($id, $input, $file);
            }
        }
        if (get_array_value($input, 'receiver_id')) {
            delete_cache("count_messages_not_seen_" . $input['receiver_id']);
            $notifiables = User::find($input['receiver_id']);
        }
            
        if (get_array_value($input, 'group_id')) {
            $messageGroup = MessageGroup::find($input['group_id']);
            $ids = $messageGroup->getIdParticipants();
            //Delete cache for each of the participants in the group
            foreach ($ids as $id) {
                delete_cache("getMessagesNotSeenGroup_" . $id);
            }
            //User List to send a notification
            $notifiables = User::whereIn('id', $ids)->where('id', '<>', Auth::id())->whereDeleted(false)->get();
        }
        return collect($messages);
    }

    /**
     * Create a new file identical to the original and save it in the server
     * After we save the message and the path to the database
     */
    public static function importFile($id, $input, $file) {
        $fileName = time() . "_" . $file->getClientOriginalName();
        $input['url'] = 'app/public/' . $file->storeAs('uploads', $fileName, 'public');
        $input['is_file'] = true;
        $input['content'] = $file->getClientOriginalName();
        return Message::updateOrCreate(['id' => $id], $input);
    }

    public function getMessageView($mine) {
        if ($this->is_file)
            return view('messaging.message-body-file', ['message' => $this, 'mine' => $mine])->render();
        return view('messaging.message-body', ['message' => $this, 'mine' => $mine])->render();
    }

    /**
     * Create a view that contains the multiple message created
     * So, the sender doesn't need to refresh the page in order to see his new messages after he saves it
     */
    public static function getMultipleMessageView($messages, $mine=true) {
        $view = "";
        foreach ($messages as $message) {
            $view .= $message->getMessageView($mine);
        }
        return $view;
    }
}
