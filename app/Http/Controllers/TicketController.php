<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Ticket;
use App\Models\UserType;
use App\Models\Department;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use App\Jobs\TicketJobNotification;
use App\Models\ItemType;
use App\Models\NeedToBuy;
use App\Models\UnitItem;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewTicketNotification;
use App\Notifications\UpdateTicketNotification;
use App\Notifications\AssignedTicketNotification;
use DB;
use Exception;

class TicketController extends Controller
{
    public  $suggestion = ["Panne connéxion", "Panne de clavier ", "Panne de souris ", "Panne de onduleur", "Panne de écran", "Panne de clavier", "Panne de casque/micro/camera"];

    public function index()
    {
        
        $user = Auth::user()->load('userJob');
        return view("tickets.index", ["basic_filter" => Ticket::createFilter($user)]);
    }
    public function modal_form()
    {
        $users = User::where('user_type_id', "<>", UserType::$_ADMIN)->whereDeleted(0)->get();
        $from = [];
        foreach ($users as $user) {
            $from[] = ["value" => $user->id, "text" => $user->sortname];
        }
        $suggestions = $this->suggestion;
        return view("tickets.modal-form", compact("from", "suggestions"));
    }
    public function store(Request $request)
    {
     
        $auth =  Auth::user();
        $data["status_id"]  =  1;
        $data["urgence_id"] = $auth->isAdmin() ? 3 : $request->urgence_id; 
        $data["author_id"]  =  $auth->id;
        $ticket = Ticket::create($request->except("_token") +  $data);
        $rh_and_admin = get_cache_rh_admin();
        $it = Department::getUserByIdDepartement(Department::$_IT);
        $notifiy_to =  collect();
        $notifiy_to = $notifiy_to->merge($it);
        $notifiy_to = $notifiy_to->merge($rh_and_admin);
        dispatch(new TicketJobNotification(  $notifiy_to, new NewTicketNotification( $ticket ,$auth)));
        return ["success" =>  true, "data" => $this->_make_row($ticket, $auth), "message" => "Le ticket a été bien créée"];
    }
    public function data_list(Request $request)
    {
        $data = [];
        $user = Auth::user();
        $tickets = Ticket::getDetail($request->all(), $user)->get();
        foreach ($tickets as $ticket) {
            $data[] = $this->_make_row($ticket, $user);
        }
        return ["data" => $data];
    }

    public function  _make_row(Ticket $ticket, $for_user)
    {
        $edit = $ticket->tag();
        $add_IT = "";
        /** Enable editable or updatable  ticket resovled or closed*/
        if (!$ticket->is_resolved()) {
            $add_IT =  modal_anchor(url("/add/ticket/assign/$ticket->id"), '<i class="text-hover-primary fas fa-user-plus " style="font-size:15px"></i>', ['title' => trans('lang.add_it')]);
            $edit =  modal_anchor(url("/ticket/edit/$ticket->id"), "#00$ticket->id", ['title' => trans('lang.edit'), 'data-modal-lg' => true]); 
        }
        return
            [
                "DT_RowId" => row_id("tickets", $ticket->id),
                "resolue" => view("tickets.column.resolve", ["ticket" => $ticket, 'for_user' => $for_user])->render(),
                "id" => $edit,
                "owner" =>  view("tickets.column.owner", ["owner" => $ticket->owner])->render(), //($ticket->owner->firstname ?? $ticket->owner->name),
                "autor" => ($ticket->author_id == $ticket->proprietor_id) ? "<span class = 'text-muted'>Lui-même</span>" : "<span class = 'text-muted'>" . ($ticket->autor->sortname) . "</span>",
                "description" => view("tickets.column.description", ["desc" =>  $ticket->description])->render(),
                "urgence" =>  $ticket->urgenceHtml,
                "status" =>  $ticket->statusHtml,
                "assign_to" =>  view("tickets.column.assign-to", ["responsibles" => $ticket->responsibles(), "add" => $add_IT, "for_user" => $for_user])->render(),
                "resolve_by" => view("tickets.column.resolve-by", ["ticket" => $ticket, 'for_user' => $for_user])->render(),
                "resolve_date" => $this->to_human_date($ticket->resolve_date, $ticket->status_id),
                "created_at" => $this->to_human_date($ticket->created_at, $ticket->status_id),
            ];
    }

    private function to_human_date($date, $status)
    {
        $class  = "dark";
        $date = Carbon::make($date);
        $return = "-";
        if ($date && $date->isToday()) {
            $return = $date->diffForHumans(null, false, true);
            $class  =  "success";
        } elseif ($date) {
            $return =  $date->format("d-M-Y");
        } elseif (!$date) {
            $return = null;;
        }
        return "<span class ='text-{$class}'>" .  $return    . "</span>";
    }

    public function add_assign_modal_form(Ticket $ticket)
    {
        return view("tickets.assign-to-modal", compact("ticket"));
    }
    public function it_not_assgned_list(Ticket $ticket)
    {
        $data = [];
        /** All it user */
        $usersJob = Department::getEmployeeByIdDepartement([Department::$_IT , Department::$_DEV])->get();
        foreach ($usersJob as $userJob) {
            if (isset($userJob->user->id)) {
                $data[] = $this->_make_row_not_assigned($userJob->user, $ticket);
            }
        }
        return ["data" => $data];
    }

    private function _make_row_not_assigned($user, $ticket)
    {
        return [
            "DT_RowId" => row_id("user", $user->id),
            "not_assigned" => view("tickets.column.assign-not-yet", ["user" => $user, "ticket" => $ticket])->render(),
            "not_assigned_input" => view("tickets.column.assign-not-yet-input", ["user" => $user, "ticket" => $ticket])->render(),
        ];
    }

    public function add_assign_to(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        $ticket->update(["assign_to" => ($request->user_ids ?  implode(",", $request->user_ids) : null)]);
        dispatch(new TicketJobNotification(  User::findMany($request->user_ids), new AssignedTicketNotification( $ticket ,Auth::user())));
        return ["success" =>  true, "row_id" => row_id("tickets", $ticket->id), "data" => $this->_make_row($ticket, Auth::user()), "message" => "Le ticket a été bien jour"];
    }
    public function set_resolve(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        if ($ticket->is_resolved()) {
            return ["success" =>  true, "info" => true, "message" =>  trans("lang.ticket_resolved")];
        }
        $auth = Auth::user();
        $auth->isIT() ?  $status_id = 5 :  /* cloturé*/  $status_id = 4; /* resolved */ 
        $ticket->update(["status_id" =>  $status_id, "resolve_by" => $auth->id, "resolve_date" => now()]);

        $this->notification_update($ticket);
        return ["success" =>  true, "row_id" => row_id("tickets", $ticket->id), "data" => $this->_make_row($ticket, Auth::user()), "message" => "Le ticket a été bien jour"];
    }
    public function edit_ticket(Request $request, Ticket $ticket)
    {
        if ($ticket->is_resolved()) {
            return view("tickets.ticket-resolved-error");
        }
        $exulde_id_status_edit = [];
        $user = Auth::user()->load('userJob');
        if (!$user->isAdmin() && !$user->isIT()) {
            $exulde_id_status_edit = [2, 3, 5];
        }
        $status = TicketStatus::drop($exulde_id_status_edit, false);
        $from =  User::whereDeleted(0)->get();
        $suggestions = $this->suggestion;
        $itemTypes = ItemType::whereDeleted(0)->orderBy("name")->get();
        $waiting_to_buy = TicketStatus::$_WAINTING_TO_BUY;
        $units = UnitItem::whereDeleted(false)->get();
        $needToBuy = collect([]);
        if ($ticket->status_id == $waiting_to_buy) {
            $needToBuy = NeedToBuy::with("itemType")->where("ticket_id", $ticket->id)->whereDeleted(0)->get();
        }
        $clearBtn = $request->clearBtn;
        return view("tickets.edit-form", compact("ticket", "status", "from", "suggestions", "itemTypes", "waiting_to_buy", "needToBuy", "clearBtn", 'units'));
    }
    public function save_edit(Request $request, Ticket $ticket)
    {
        DB::beginTransaction();
        try {
            if ($ticket->is_resolved()) {
                return ["success" =>  true, "info" => true, "message" =>  trans("lang.ticket_resolved") . " T-#00$ticket->id"];
            }
            $data = [];
            if (in_array($request->status_id, TicketStatus::$_RESOLVED)) {
                $data["resolve_by"] = Auth::id();
            }
    
            if ($request->status_id == TicketStatus::$_WAINTING_TO_BUY) {
                NeedToBuy::saveNeed($ticket, $request->need_to_buy_id, $request->item_type_id, $request->quantity, $request->units_id);
            }
    
            $ticket->update($request->except("_token", "ticket_id", 'proprietor_id')  + $data);
            $this->notification_update($ticket);
            DB::commit();
            return ["success" =>  true, "row_id" => row_id("tickets", $ticket->id), "data" => $this->_make_row($ticket, Auth::user()), "message" => "Le ticket a été bien a jour"];
        }
        catch(Exception $e) {
            DB::rollBack();
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    private function notification_update(Ticket $ticket){
        $notifiy_to =  collect();
        if ($ticket->author_id == $ticket->proprietor_id){
            $notifiy_to->push($ticket->owner);
        }else{
            $notifiy_to->push($ticket->owner,$ticket->autor);
        } 
        /** add  */
        if ($ticket->assign_to) {
            $notifiy_to = $notifiy_to->merge($ticket->responsibles);
        }
        $departemnt = Department::getUserByIdDepartement(Department::$_IT);
        if ($departemnt) {
            $notifiy_to = $notifiy_to->merge($departemnt);
        }
        $rh_and_admin = get_cache_rh_admin();
        if ($rh_and_admin) {
            $notifiy_to = $notifiy_to->merge($rh_and_admin);
        }
        dispatch(new TicketJobNotification($notifiy_to, new UpdateTicketNotification( $ticket ,Auth::user())));
    }

    public function desktop_notification(Request $request)
    {
        return [];
        // return Artisan::call('desktop:notification',["title" => $request->title , "message" => $request->message ]);
    }
}