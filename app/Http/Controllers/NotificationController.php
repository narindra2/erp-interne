<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\DayOff;
use App\Models\Ticket;
use App\Models\ContractType;
use App\Models\TicketStatus;
use App\Notifications\RedisTestNotification;
use Illuminate\Http\Request;
use Notification;

class NotificationController extends Controller
{
    private function count_day_no_response($details = false) {
        $nb  = DayOff::countDayOffWithoutResponse();
        if (!$details) {
            return $nb;
        }
        return ["has" => $nb > 0,  "title" => "Congé et Permission",  "content"=> "On a " . ($nb > 1 ?  "$nb demandes" : "une demandes " ) . " de congés en attente."];
       
    }
    private function count_tickets_not_resoved($details = false) {
        $query = Ticket::whereRaw('FIND_IN_SET(' . auth()->id() . ', assign_to)')
                        ->whereNotIn("status_id" ,TicketStatus::$_RESOLVED );
        if (!$details) {
            return $query;
        }
        return ["has" => $query->count() > 0, "title" => "Ticket",  "content"=> "  Vous aves  {$query->count()} non résolue."];
    }
    private function get_user_pe_completed($details = false) {
        $query =  User::whereDeleted(0)
                    ->where('hiring_date', '<=', Carbon::now()->subdays(ContractType::$_PE_END_DAY_AFTER_HIRING_DATE))
                    ->whereHas('userJob', function ($query) {
                        $query->where('contract_type_id', ContractType::$_PE_CONTRAT);
                    })->get();
        if (!$details) {
            return $query;
        }
        $verb = $query->count() > 1 ? "ont" : "a";
        $names = $query->implode("sortname" , ",");
        return ["has" =>$query->count() > 0, "title" => "Période d'essai",  "content"=> "$names {$verb}  terminé leur période essaie"];
    }
    public function check_permanent_notification(Request $request)
    {
        $auth = Auth::user();
        $message = modal_anchor(url('notification/list/permanent'), 'Des notifications ... <u>Voir</u>' , ['title' => "Détail des notifications" ,"class" => "text-secondary", "data-drawer" =>true]);
        if ($this->get_user_pe_completed()->count() && $auth->isRhOrAdmin()) {
            return ["success" => true ,"message" => $message ];
        }
        if ($this->count_day_no_response() && $auth->isRhOrAdmin()) {
            return ["success" => true ,"message" => $message ];
        }
        if ($this->count_tickets_not_resoved()->count()) {
            return ["success" => true ,"message" => $message ];
        }
    }
    public function list_permanent_notification()
    {
        $pe = $this->get_user_pe_completed(true);
        if (get_array_value( $pe , "has")) {
            $notifications[] = $pe;
        }
        $day_off = $this->count_day_no_response(true);
        if (get_array_value( $day_off , "has")) {
            $notifications[] = $day_off;
        }
        $tickets = $this->count_tickets_not_resoved(true);
        if (get_array_value( $tickets , "has")) {
            $notifications[] = $tickets;
        }
        return view("notifications.permanent-notification-detail" ,["notifications" => $notifications]);
    }

    public function redis()
    {
        Notification::send(User::find(2), new RedisTestNotification("test"));
        return ["success" => true , "message" => "Ok"];
    }
}
