<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    private static $contact_query = "select t2.* from 
    (select MAX(created_at) as created_at, IF (sender_id=%s, receiver_id, sender_id) as contact_id
    from messages where sender_id=%s or receiver_id=%s group by contact_id) as t1 join (
    select *, IF (sender_id=%s, receiver_id, sender_id) as contact_id
    from messages where sender_id=%s or receiver_id=%s
    ) as t2 on t1.contact_id = t2.contact_id and t1.created_at = t2.created_at order by t2.created_at desc";

    public static function fill_contact_query($userID) {
        return sprintf(self::$contact_query, $userID, $userID, $userID, $userID, $userID, $userID);
    }

    public static function getContact($userID) {
        $contacts = DB::select(self::fill_contact_query($userID));
        self::setUserContact($contacts);
        return $contacts;
    }

    public static function getAllContactId($contacts) {
        $contactIds = [];
        foreach ($contacts as $contact) {
            $contactIds[] = $contact->contact_id;
        }
        return $contactIds;
    }

    public static function setUserContact(&$contacts) {
        $contactIds = self::getAllContactId($contacts);
        $users = User::whereIn('id', $contactIds)->get();
        foreach ($contacts as $contact) {
            foreach ($users as $user) {
                if ($contact->contact_id == $user->id) {
                    $contact->user = $user;
                    break;
                }
            }
            if (strlen($contact->content) > 20)
                $contact->content = substr($contact->content, 0, 20) . " ...";
        }
    }

    public static function createContactInstanceByMessage(Message $message) {
        $contact = new Contact();
        if (strlen($contact->content) > 20)
            $contact->content = substr($message->content, 0, 20) . " ...";
        else 
            $contact->content = $message->content;
        if ($message->_my_message) {
            $contact->contact_id = $message->receiver_id;
            $contact->user = $message->receiver;
        } else {
            $contact->contact_id = $message->sender_id;
            $contact->user = $message->sender;
        }
        $contact->created_at = $message->created_at;
        return $contact;
    }
}
