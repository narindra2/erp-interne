<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageGroupRequest;
use App\Http\Requests\SaveMessageReactionRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\MessageGroupParticipantResource;
use App\Jobs\MessagingNotification;
use App\Models\Contact;
use App\Models\Message;
use App\Models\MessageGroup;
use App\Models\MessageGroupParticipant;
use App\Models\MessageSeen;
use App\Models\MessageView;
use App\Models\User;
use App\Models\UserReactionMessage;
use App\Notifications\NewMessageNotification;
use Auth;
use DB;
use Error;
use Exception;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function index() {
        $data = [];
        $data['user'] = Auth::user();
        $data['isAdmin'] = Auth::user()->isAdmin();
        $data['contacts'] = Contact::getContact(Auth::id());
        $data['groups'] = MessageGroupParticipant::getUserGroup(Auth::user());
        return view('messaging.index', $data);
    }

    public function viewContactModal(Request $request) {
        $data = [];
        $data['contacts'] = Contact::getContact(Auth::id());
        return ['view' => view('messaging.modals.contacts', $data)->render()];
    }

    public function viewGroupModal(Request $request) {
        $data = [];
        $user = Auth::user();
        $data['groups'] = MessageGroup::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
            $query->whereDeleted(0);
        })->get();
        return ['view' => view('messaging.modals.contact-groups', $data)->render()];
    }

    public function getDiscussionPage(User $user, Request $request) {
        $offset = $request->offset ? $request->offset : 0;
        $messages = Message::getDiscussion(Auth::user(), $user, $offset);
        if ($offset) {
            $offset += 10;
            return ['view' => Message::getMultipleMessageView($messages), 'offset' => $offset, 'success' => true];
        }
        $data = [];
        $data['user'] = $user;
        $data['offset'] = $offset + 10;
        $data['default_offset'] = 10;
        $data['messages'] = $messages;
        return view('messaging.discussion', $data)->render();
    }

    public function getDiscussionPageGroup(MessageGroup $messageGroup, Request $request) {
        $offset = $request->offset ? $request->offset : 0;
        $messages = Message::getDiscussionGroup($messageGroup, Auth::user(), $offset);
        if ($offset) {
            $offset += 10;
            return ['view' => Message::getMultipleMessageView($messages, true), 'offset' => $offset, 'success' => true];
        }
        $data = [];
        $data['offset'] = $offset + 10;
        $data['default_offset'] = 10;
        $data['messages'] = Message::getDiscussionGroup($messageGroup, Auth::user(), $offset);
        $data['messageGroup'] = $messageGroup;
        $data['nb_participants'] = MessageGroupParticipant::whereDeleted(0)->where('message_group_id', $messageGroup->id)->count();
        return view('messaging.discussion-group', $data)->render();
    }

    public function modalMessage(Request $request) {
        $offset = $request->offset ? $request->offset : 0;
        $view = "";
        if ($request->group_id) {
            $messageGroup = MessageGroup::find($request->group_id);
            MessageView::makeMessagesReadGroup($messageGroup, Auth::user());
            $messages = Message::getDiscussionGroup($messageGroup, auth()->user(), $offset);
            $view = view('messaging.modals.index-group', ['messageGroup' => $messageGroup, 'messages' => $messages, 'offset' => $offset + 10])->render();
        }       
        else {
            $contact = User::find($request->sender_id);
            MessageView::makeMessagesRead($contact, Auth::user());
            // dispatch(function () use ($contact){
            //     // \Notification::send($notify_to, new TaskCommentNotification($task, $auth));
            // })->afterResponse();
            $messages = Message::getDiscussion(Auth::user(), $contact, $offset);
            $view = view('messaging.modals.index', ['contact' => $contact, 'messages' => $messages, 'offset' => $offset + 10])->render();
        }
        return ['view' => $view];
    }

    // public function getDiscussion(User $user, Request $request) {
    //     return [
    //         'data' => Message::getDiscussion(Auth::user(), $user, $request->offset) 
    //     ];
    // }

    /**
     * 
     * Get Data from request and save it
     */
    public function store(SendMessageRequest $request) {
        DB::beginTransaction();
        try {
            $notifiables = new User(); //Notifiables variable is the user list to send notification after a new message
            $messageGroup = null; 
            $messages = Message::storeMessage($request->except(["_token"]), $request->file("files"), $notifiables, $messageGroup);
            $conversations = Message::getMultipleMessageView($messages);
            $conversationsAnotherUser = Message::getMultipleMessageView($messages, false);
            $contact = Contact::createContactInstanceByMessage($messages->last());
            $contactView = "";

            if (!$request->group_id) {
                $contactView = view('messaging.contact-item', ['contact' => $contact])->render();
            }
            \Notification::send($notifiables, new NewMessageNotification(Auth::user(), $messageGroup, $messages, $conversationsAnotherUser, $contactView));
            DB::commit();
            return ['success' => true, 'view' => $conversations, 'contactView' => $contactView, 'contact' => $contact];
        }
        catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'view' => '', 'contactView' => '', 'contact' => '', 'message' => $e->getMessage()];
        }
        catch (Error $e) {
            DB::rollBack();
            return ['success' => false, 'view' => '', 'contactView' => '', 'contact' => '', 'message' => $e->getMessage()];
        }
    }

    public function downloadAttachedFile(Message $message) {
        $url = storage_path($message->url);
        return response()->download($url);
    }

    public function searchUser(Request $request) {
        $data = [];
        $term = $request->searchTerm ?? $request->term;
        if ( $term) {
            $q = User::where("id" ,"<>", Auth::id() )->whereDeleted(0);
           if(!in_array( $term,["all","tous"])){
                $q->where("name", "like", "%$term%")->orWhere("firstname", "like", "%$term%");
           }
           $users =$q->orderBy('name')->get();
            foreach ($users as $user) {
                $data[] = [
                    'id' => $user->id,
                    'text' => $user->fullname
                ];
            }
        }
        if( $request->term){
            return ["results" => $data];
        }
        return response()->json($data);
    }

    public function getModalUserListSeenMessage(Message $message) {
        $data = [];
        $data['messagesSeen'] = MessageSeen::getUserWhoSawMessage($message);
        return view('messaging.messageSeen.modal-list-user', $data)->render();
    }

    public function modalSearchDiscussion (Request $request) {
        $data = [];
        $data['users'] = User::whereDeleted(0)->where('id', '<>', Auth::id())->orderBy('name')->get();
        return view('messaging.modals.search-discussion', $data)->render();
    }

    //-------------------------------------------------------GROUP------------------------------------------------------

    public function formGroupModal(MessageGroup $messageGroup) {
        return view('messaging.groups.form-group-modal', compact('messageGroup'));
    }

    public function storeGroup(MessageGroupRequest $request) {
        $message = "";
        $messageGroup = MessageGroup::saveOrUpdateMessageGroup($request->input(), Auth::user(), $message);
        return ['success' => true, 'message' => $message];
    }

    public function formGroupParticipantsModal(MessageGroup $messageGroup) {
        $data = [];
        $data['messageGroup'] = $messageGroup;
        return view('messaging.groups.form-participants-modal', $data);
    }

    public function getDataOfParticipants(MessageGroup $messageGroup) {
        $participants = MessageGroupParticipant::with('user')->where('message_group_id', $messageGroup->id)->whereDeleted(0)->get();
        return MessageGroupParticipantResource::collection($participants);
    }

    public function deleteMember(MessageGroupParticipant $messageGroupParticipant) {
        $messageGroupParticipant->deleted = true;
        $messageGroupParticipant->save();
        return ['success' => true, 'tr_id' => row_id("participants_id", $messageGroupParticipant->id)];
    }

    public function addUserInGroup(Request $request) {
        $messageGroupParticipant = MessageGroupParticipant::addUserToGroup($request->input());
        $messageGroupParticipant->load('user');
        return ['success' => true, 'data' => new MessageGroupParticipantResource($messageGroupParticipant)];
    }

    //Reaction

    public function saveReaction(SaveMessageReactionRequest $request) {
        $addition = 1;
        $userReactionMessage = UserReactionMessage::saveUserReaction($request->input(), Auth::user(), $addition);
        $title = "<p>" . Auth::user()->sortname . "</p>";
        $view = view('messaging.modals.reactions.reaction-count', ['count' => 1, 'reaction' => getIconsReaction()->where('id', $userReactionMessage->message_reaction_id)->first(), 'message_id' => $userReactionMessage->message_id, 'title' => $title ])->render();
        return ['success' => true, 'addition' => $addition, 'userReactionMessage' => $userReactionMessage, 'view' => $view];
    }
}
