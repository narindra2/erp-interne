<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskFile;
use App\Models\TaskStatus;
use App\Models\TaskComment;
use App\Models\TaskSection;
use Illuminate\Http\Request;
use App\Models\TaskCheckList;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AddTaskResquest;
use App\Notifications\TaskCommentNotification;
use App\Notifications\TaskFileAddedNotification;
use App\Notifications\TaskSectionCreatedOrUpdatedNotification;

class TaskController extends Controller
{
    /** Board kanban */
    public function index(Request $request): object
    {
        $auth = Auth::user();
        $sections = $auth->isAdmin() ? TaskSection::whereDeleted(0)->orderBy("created_at","ASC")->get() : $auth->sectionsTask()->whereDeleted(0)->orderBy("created_at","ASC")->get();
        return view("tasks.index", compact("sections"));
    }
    /**Update task from darg and drop */
    public function update(Request $request): array
    {
        $task = Task::find($request->task_id);
        $task->status_id = str_replace("board-id-", "", $request->target_id);
        $task->save();
        return ["success" => true, "board_id" => "board-id-" . $task->status_id, "data" => $this->_make_item_board($task,  true)];
    }
    /** Update/detail task modal */
    public function modal_form(Request $request)
    {
        $section_id = $request->section_id;
        return view("tasks.crud.modal-form", ["task" => new Task, "users" => $this->get_user_tag_list($section_id), "section_id" => $section_id, "recurring_type" => Task::$recurring_type, "default" =>  null]);
    }
    /** Update or create  a task  from modal*/
    public function save(AddTaskResquest $request)
    {
        $auth = Auth::user();
        $message  = trans("lang.updated-task");
        $data = ["assign_to" => collect(json_decode($request->users, true))->implode("value",",")];
        if (!$request->task_id) {
            $data["status_id"] = Task::getToDoBoardId($request->section_id);
            $data["creator"]   = $auth->id;
            $message = trans("lang.created-task");
        }
        $data["recurring"] = 0;
        $data["recurring_type"] = null;
        $data["section_id"] = $request->section_id;
        if ($request->recurring_type) {
            $data["recurring"] = 1;
            $data["recurring_type"] = $request->recurring_type;
            $data["start_date_recurring"] = $request->start_date_recurring ? Carbon::parse($request->start_date_recurring)->format("Y-m-d") : Carbon::now();
            $data["recurring_detail"] = serialize(["nb_days" =>  $request->nb_days, "day_of_week" => $request->day_of_week]);
        }
        $datas = $request->all();
        if ($request->start_deadline_date) {
            $datas["start_deadline_date"] = to_date($request->start_deadline_date);
            $datas["end_deadline_date"] = to_date($request->end_deadline_date);
        }
        $datas["archived"] = 0;
        $datas["ribbon"] = $request->ribbon;
        if ($request->archived) {
            $datas["archived"] = 1;
            $datas["status_id"] =  Task::getArchivedBoardId($request->section_id);
        }
        if ($request->section_id) {
            $section = TaskSection::find($request->section_id);
            if ($section->members_can_not("can_add_task")) {
                return ["success" => false, "message" => "Vous ne pouvez pas effectuer ajouter des tâches (permission non aloué)"];
            }
                if ($request->archived) {
                if ($section->members_can_not("can_archive_task")) {
                    return ["success" => false, "message" => "Vous ne pouvez pas effectuer archive (permission non aloué) "];
                }
            }
            if ($request->deleted) {
                if ($section->members_can_not("can_delete_task")) {
                    return ["success" => false, "message" => "Vous ne pouvez pas effectuer suppresion (permission non aloué)"];
                }
            }
        }
        $old_task = Task::with(["section"])->find($request->task_id);
        if ($old_task) {
            if ($old_task->section->members_can_not("can_update_or_edit_member_task")) {
                return ["success" => false, "message" => "Vous ne pouvez pas effectuer modification du tache de {$old_task->autor->sortname} (permission non aloué)"];
            }

        }
        $task = Task::updateOrCreate(["id" => $request->task_id], $datas +  $data);

        $this->handle_files($request, $auth, $task);
        $this->handle_checklists($request, $auth, $task);
        $this->handle_comment($request, $auth, $task);
        return ["success" => true, "deleted" => $request->deleted, "message" => $message, "board_id" => "board-id-" . $task->status->id, "data" => $this->_make_item_board($task)];
    }
    /** Upload files  and save it */
    private function handle_files(Request $request, $auth, $task)
    {
        if ($request->hasFile("files")) {
            $files = [];
            foreach ($request->file('files') as $file) {
                $file_info = upload($file, "/task-files/task-$task->id", "public");
                $files[] = [
                    "name" => $file_info["name"],
                    "originale_name" => $file_info["originale_name"],
                    "uploaded_by" => $auth->id,
                ];
            }
            $task->files()->createMany($files);
        }
    }
    /** Save comment  and send nofication */
    private function handle_comment(Request $request, $auth, $task)
    {
        if ($request->comment) {
            TaskComment::updateOrCreate(["id" => $request->comment_id], ["content" =>  $request->comment, "user_id" =>  $auth->id, "task_id" => $task->id]);
            if (!$request->comment_id && !$request->task_id) {
                $this->send_comment_notification($request->task_id, $auth);
            }
        }
    }
    /** Save checklist */
    private function handle_checklists(Request $request, $auth, $task)
    {
        if (!$request->checklists) {
            return;
        }
        $checklists = []; $has = false;
        foreach($request->checklists as $input){
            if ($input) {
                $has = true;
                $checklists[] = ["description" => $input ,"user_id" => $auth->id ];
            }
        }
        if($has){
            $task->checkLists()->createMany($checklists);
        }
    }
    
    /** Task detail on modal */
    public function detail(Request $request): object
    {
        $assign_to = [];
        $task = Task::with(["comments", "section.colones" => function($colone){
            $colone->orderBy("order_board","ASC");
        }, "files.uploader","checkLists"])->find($request->task_id);
        if ($task->section->is_not_member()) {
            return view("tasks.error-page", ["message" => "Desolé vous n'etes pas membres dans cette section de tâche (permission non aloué)"]);
        }
        if ($task->assign_to) {
            foreach ($task->responsibles as $user) {
                $assign_to[] = $this->_make_user_tag($user);
            }
        }
        return view("tasks.crud.modal-form", ["task" =>  $task, "users" => $this->get_user_tag_list($task->section_id), "recurring_type" => Task::$recurring_type, "default" => json_encode($assign_to)]);
    }
    public function add_board_modal(Request $request)
    {
        $status  = TaskStatus::with(["section"])->find($request->status_id) ?? new TaskStatus;
        return view("tasks.crud.board-modal", ["status" =>  $status, "section_id" => $request->section_id]);
    }
    public function add_status_board(Request $request)
    {
        if (!$request->title) {
            return ["success" => false, "message" => "Veuillez indiquer le label svp."];
        }
        $data = [];
        if (!$request->status_id) {
            $data["order_board"] = 100;
            $data["acronym"] = str_replace(" ", "_", $request->title);
        }
        if ($request->status_id && in_array($request->acronym, ["TO_DO", "FINISHED", "ARCHIVED"])) {
            $status = TaskStatus::find($request->status_id);
            if ($status->title != $request->title) {
                return ["success" => false, "message" => "Le nom de ce colone est non modifiable."];
            }
        }
        $status_board = TaskStatus::updateOrCreate(["id" => $request->status_id], ["title" => $request->title, "section_id" => $request->section_id, "class" => $request->class ?? "white", "deleted" => $request->deleted ?? 0] + $data);
        return ["success" => true, "message" => $request->deleted ? "Supppression avec succes" :  "Ajout avec succes", "board_id" => $request->status_id, "deleted" => $request->deleted, "board" => $this->_make_board($status_board)];
    }
    /**  Update Task items order  one board */
    public function update_order_item(Request $request): array
    {
        Task::upsert($request->data, ['id'], ["order_on_board"]);
        return ["success" => true];
    }
    /**  Update order board */
    public function update_order_board(Request $request)
    {
        $section= TaskSection::find($request->section_id);
        if ($section->members_can_not("can_update_column")) {
            ["success" => false, "message" => "Cet foctionnalité n'est pas permis "];
        }
        TaskStatus::upsert($request->data, ['id'], ["order_board"]);
        return ["success" => true, "message" => "Ordre enregistrer ! (pour les autres utilisateurs aussi)"];
    }
    /** Prepare  list users */
    private function get_user_tag_list($section_id = 0): array
    {
        $data = [];
        if ($section_id) {
            $users = TaskSection::find($section_id)->members()->with(["userJob.job"])->get();
        } else {
            $users = User::with(["userJob.job"])->whereDeleted(0)->where("users.id", "<>", Auth::id())->get();
        }
        foreach ($users as $user) {
            $data[] = $this->_make_user_tag($user);
        }
        return $data;
    }
    /** Make list users */
    private function _make_user_tag($user): array
    {
        return [
            "value" => $user->id,
            "name" => $user->sortname,
            "avatar" => $user->avatarUrl,
            "job" => ($user->userJob && $user->userJob->job) ? $user->userJob->job->name : "",
        ];
    }
    /** Data sources */
    public function kanban_data(Request $request): array
    {
        $boards = $task_todo = $task_recycled = [];
        $status_todo = null;
        $tasks_status = TaskStatus::getDetail($request->all())->get();
        foreach ($tasks_status as $status) {
            $status->acronym == "TO_DO" ? $status_todo = $status : "";
            $tasks = [];
            if ($status->tasks_count) {
                foreach ($status->tasks as $task) {
                    if ($status->acronym == "TO_DO") {
                        $task_todo[] = $this->_make_item_board($task);
                    } else {
                        if ($task->recurring  && $task->need_recycle()) {
                            $task_recycled[] = $this->handle_recurring_task($task);
                        } else {
                            $tasks[] =  $this->_make_item_board($task);
                        }
                    }
                }
            }
            if ($status->acronym != "TO_DO") {
                $boards[] = $this->_make_board($status, $tasks);
            }
        }
        /** Add boards todo item's list i first of array*/
        array_unshift($boards, $this->_make_board($status_todo, array_merge($task_recycled, $task_todo)));
        return ["success" => true, "data" => $boards];
    }
    /** Do recycle task */
    public function handle_recurring_task(Task $task): array
    {
        $task->set_recycle();
        return $this->_make_item_board($task);;
    }
    /** Make boards list */
    public function _make_board($board = null, $items = []): array
    {
        return [
            "id" => 'board-id-' . $board->id,
            'title' =>  modal_anchor(url('/task/add/board/status-modal'), strtoupper($board->title), ['title' => trans('lang.update-board'), 'class' => "text-$board->class", "data-post-status_id" => $board->id, "data-post-section_id" => $board->section_id]),
            'class' => "light-$board->class",
            'item' => $items,
        ];
    }
    /** Make boards items  */
    public function _make_item_board(Task $task, $update = false): array
    {
        return [
            'id' => $task->id,
            'title' =>  view("tasks.kanban.item", ["task" => $task, "update" => $update])->render(),
            "task-id" => $task->id,
            'class' =>  ["border", "border-hover", "shadow", "p-3", "mb-5", "bg-whit", "rounded", "alert", "alert-info"], /** Ne mettez pas d'espace */
           
        ];
    }
    /** Save task comment*/
    public function save_comment(Request $request)
    {
        if (!$request->comment) {
            return ["success" => false, "message" => "Une commentaire ne peut pas etre vide!"];
        }
        $auth = Auth::user();
        $comment = TaskComment::updateOrCreate(["id" => $request->comment_id], ["content" =>  $request->comment, "user_id" => $auth->id, "task_id" => $request->task_id]);
        if (!$request->comment_id) {
            $this->send_comment_notification($request->task_id, $auth);
        }
        return ["success" => true, "comment" => view("tasks.comments.item", ["comment" => $comment])->render()];
    }
    /**    Send task comment notification*/
    private function send_comment_notification($task_id, $auth)
    {
        $task = Task::find($task_id);
        $notify_to = $task->responsibles;
        if ($task->creator !=  $auth->id) {
            $notify_to = $notify_to->push($task->autor);
        }
        dispatch(function () use ($notify_to, $task, $auth) {
            \Notification::send($notify_to, new TaskCommentNotification($task, $auth));
        })->afterResponse();
    }
    /** Delete task comment*/
    public function delete_comment(Request $request)
    {
        dispatch(function () use ($request) {
            TaskComment::where('id', $request->comment_id)->update(['deleted' => 1]);
        })->afterResponse();
        return ["success" => true];
    }
    //** Search a task */
    public function search_task(Request $request)
    {
        $data = [];
        $tasks = Task::where(function ($query) use ($request) {
            if (!in_array(strtolower($request->term), ["tous", "all", "toutes", "tout"])) {
                $auth = Auth::user();
                $section = TaskSection::find($request->section_id);
                if ($section->members_can_not("can_access_members_task")) {
                    $query->where(function ($q) use ($auth) {
                        $q->whereRaw(' FIND_IN_SET(' . $auth->id . ',assign_to)')->orWhere("creator", $auth->id);
                    });
                }
                $query->where("title", 'like', '%' . $request->term . '%');
                $query->orWhere("description", 'like', '%' . $request->term . '%');
            }
        })->whereDeleted(0)->where("section_id", $request->section_id)->get();
        foreach ($tasks as $task) {
            $data[] = ["id" => $task->id, "text" => $task->title];
        }
        return ["results" => $data];
    }
    private function sections_permission(){
        $permmisions = [];
        $permmisions[] = ["access" => "can_add_task" ,"description" => " Les membres peuvent ajouter des tâches." ];
        $permmisions[] = ["access" => "can_update_or_edit_member_task","danger" => true ,"description" => " Les membres peuvent editer ou mettre à jour les tâches des autres membres." ];
        $permmisions[] = ["access" => "can_add_members" ,"description" => " Les membres peuvent ajouter des tâches membres. " ];
        $permmisions[] = ["access" => "can_access_members_task" ,"description" => " Les membres peuvent voir les tâches des autres membres. " ];
        $permmisions[] = ["access" => "can_add_column" ,"description" => " Les membres peuvent ajouter des colones. " ];
        $permmisions[] = ["access" => "can_update_column" ,"description" => " Les membres peuvent mettre à jour et deplacer les colones. " ];
        $permmisions[] = ["access" => "can_delete_task" ,"danger" => true, "description" => " Les membres peuvent supprimer les tâches. " ];
        $permmisions[] = ["access" => "can_archive_task" ,"description" => " Les membres peuvent acrchiver les tâches. " ];
        return $permmisions;
    }
    public function task_section_modal(Request $request)
    {
        $section = $request->section_id ? TaskSection::find($request->section_id) : new TaskSection();
        $members = $this->get_user_tag_list();
        $permissions = $this->sections_permission();
        return view("tasks.crud.section-modal", ["section" => $section, "members" => $members ,"permissions" => $permissions]);
    }
    public function task_section_save(Request $request)
    {
        if (!$request->title) {
            return ["success" => false, "message" => "Veuillez nommé le nom du section svp !"];
        }
        $auth =  Auth::user();
        $permissions = [];
        if ($request->section_id) {
            $section = TaskSection::find($request->section_id);
            if ($section->creator_id != Auth::id()) {
                return ["success" => false, "message" => "Vous ne pouvez pas  effectuer cette opération (Seul le createur de cette section le peut ! )"];
            }
        }
        $allowed_permissions = $this->sections_permission();
        foreach($allowed_permissions as $permission){
            $access = get_array_value($permission,"access");
            if ($request->$access == "1") {
                $permissions[] = $access;
            }
        }
        $section = TaskSection::updateOrCreate(["id" => $request->section_id], ["title" => $request->title, "creator_id" => $auth->id,"permissions" => $permissions]);
        $message = "Modification bien effectué";
        if (!$request->section_id) {
            $members = [];
            $members[] = $auth->id;
            $message = "Espace tâche bien créee.";
            dispatch(function () use ($section, $members, $request) {
                $section->colones()->createMany([
                    ['title' => 'A faire', "acronym" => "TO_DO", "class" => "danger"],
                    ['title' => 'Terminer', "acronym" => "FINISHED", "class" => "success"],
                    ['title' => 'Archive', "acronym" => "ARCHIVED", "class" => "warning", "order_board" => 101],
                ]);
                if ($request->users) {
                    $members = array_merge($members, collect(json_decode($request->users, true))->pluck("value")->toArray());
                }
                $section->members()->sync($members);
                $section->load("members");
                \Notification::send($section->members, new TaskSectionCreatedOrUpdatedNotification($section, Auth::user()));
            })->afterResponse();
        }
        return ["success" => true, "message" => $message, "update" => ($request->section_id ?? 0), "title" => $section->title,  "data" => view("tasks.crud.section-item", ["section" => $section, "for_user" => $auth])->render()];
    }
    public function load_kanban_section(Request $request)
    {
        $auth =  Auth::user();
        $section = TaskSection::where("id", "=", $request->section_id)->whereDeleted(0)->first();
        if (isset($section->id)) {
            $section->load("members:id,name,firstname,deleted,avatar");
            if ($section->is_not_member($auth->id)) {
                return view("tasks.error-page", ["message" => "Desolé vous n'etes pas membres dans cette section de tâche"]);
            }
            $basic_filter = Task::createFilter(["section" =>  $section]);
            return view("tasks.kanban.kanban", compact("basic_filter", "auth", "section"));
        }
        return view("tasks.error-page");
    }
    public function members_modal_form(Request $request)
    {
        /** Get user no in memebers */
        $list = [];
        $section = TaskSection::find($request->section_id);
        if ($section->members_can_not("can_add_members")) {
            return view("tasks.error-page", ["message" => "Cet fontionnalite ajout de membres est desactivé"]);
        }
        $members= DB::table("task_sections_members")->where("section_id", $request->section_id)->pluck("user_id")->toArray();
        $users = User::with(["userJob.job"])
            ->select("id", "name", "firstname", "deleted", "avatar")
            ->whereNotIn("id", $members)
            ->whereDeleted(0)->orderBy("firstname")->get();
        foreach ($users as $user) {
            $list[] = $this->_make_user_tag($user);
        }
        return view("tasks.crud.members-modal-form", ["section_id" => $request->section_id, "users" => $list]);
    }
    public function members_section_task_data(Request $request)
    {
        $data = [];
        $section = TaskSection::with(["members" => function ($user) {
            $user->select(["users.id", "name", "firstname", "deleted", "avatar"])->with(["userJob.job"])->whereDeleted(0)->orderBy("firstname");
        }])->find($request->section_id);
        foreach ($section->members as $user) {
            $data[] = $this->_make_row_members($user,  $section);
        }
        return ["data" => $data];
    }
    public function save_new_members(Request $request)
    {
        if (!$request->users) {
            return ["success" => false, "message" => "Merci d' ajouter les nouveaux membres svp!"];
        }
        $data =   [];
        $avatar_html = "";
        $section = TaskSection::find($request->section_id);
        
        if ($section->members_can_not("can_add_members")) {
            return ["success" => false, "message" => "Cet enreigistrment n'est pas permis"];
        }
        $users = User::with(["userJob.job"])->findMany(collect(json_decode($request->users, true))->pluck("value")->toArray());
        dispatch(function () use ($section, $users) {
            $section->members()->syncWithoutDetaching($users->pluck("id")->toarray());
            \Notification::send($users, new TaskSectionCreatedOrUpdatedNotification($section, Auth::user()));
        })->afterResponse();
        foreach ($users as $user) {
            $data[] = $this->_make_row_members($user, $section);
            $avatar_html .= view("tasks.crud.member-avatar", ["user" => $user])->render();
        }
        return ["success" => true, "message" => "Ajout avec succés.", "data" => $data, "avatar_html" => $avatar_html];
    }
    private function  _make_row_members(User $user, TaskSection $section)
    {
        $job = $user->userJob ? "<span class='badge badge-light-primary'>{$user->userJob->job->name}</span>" : "";
        return [
            "id" => $user->id,
            "name" => $user->sortname,
            "user" => '<div class="d-flex align-items-center">
                        <div class="symbol symbol-30px symbol-circle">
                            <img alt="Pic" src="' . $user->avatarUrl . '" > &nbsp;
                        </div>
                        <div class=" d-flex">
                            <span> ' . $user->sortname . "  " .   $job . ' </span>
                        </div>
                            </div>',
            "action" => $section->creator_id == $user->id ? "" : js_anchor('<i class="fas fa-trash text-danger " style="font-size:12px" ></i>', ["data-action-url" => url("/task/section/members/delete/$section->id"), "class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete", "data-post-user_id" => $user->id]),
        ];
    }
    public function  delete_member_section(TaskSection $section, Request $request)
    {
        if ($section->creator_id == $request->user_id) {
            return ["success" => false, "message" => "Impossible de supprimer"];
        }
        if ($section->creator_id != Auth::id()) {
            return ["success" => false, "message" => "Vous ne pouvez pas  effectuer cette suppression"];
        }
        if ($request->input("cancel")) {
            dispatch(function () use ($request, $section) {
                $section->members()->syncWithoutDetaching([$request->user_id]);
            })->afterResponse();
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row_members(User::find($request->user_id), $section)];
        } else {
            dispatch(function () use ($request, $section) {
                $section->members()->detach($request->user_id);
            })->afterResponse();
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
    public function  delete_section(Request $request)
    {
        $auth =  Auth::user();
        $section = TaskSection::find($request->section_id);
        if ($section->creator_id != $auth->id) {
            return ["success" => false, "message" => "Vous ne pouvez pas  effectuer cette suppression"];
        }
        dispatch(function () use ($section, $auth) {
            $section->deleted = 1;
            $section->save();
            $section->load("members");
            \Notification::send($section->members, new TaskSectionCreatedOrUpdatedNotification($section, $auth, [], true));
        })->afterResponse();
        return ["success" => true, "message" => trans("lang.success_deleted")];
    }
    public function  task_files_data(Request $request)
    {
        $data = [];
        $files = TaskFile::where("task_id",  $request->task_id)->whereDeleted(0)->get();
        $auth_id = Auth::id();
        foreach ($files as $file) {
            $data[] = $this->_make_file_task_row($file, $auth_id);
        }
        return ["data" => $data];
    }
    private function _make_file_task_row($file, $auth_id)
    {
        $name = "<a href='$file->uri' download> $file->originale_name</a> <br><i><span class='text-gray-400 mt-1 fw-semibold '>Par : {$file->uploader->sortname}  , Date : $file->created_date </span><i>";
        $actions = "<a href='$file->uri' download> <i class='fas fa-cloud-download-alt text-primary fs-4' title='Télécharger'></i></a>";
        if ($file->uploaded_by == $auth_id) {
            $actions .= " &nbsp;&nbsp;" . js_anchor('<i class="fas fa-trash text-danger" title="Supprimer"></i>', ["data-action-url" => url("/task/file/delete"), "data-post-file_id" => $file->id,  "title" => "Supprimé", "data-action" => "delete"]);
        }
        return [
            "name" => $name,
            "actions" => $actions,
        ];
    }
    public function add_files_task(Request $request)
    {
        if (!$request->hasFile("other_file")) {
            return ["success" => false, "message" =>  "Veuillez inserer le fichier"];
        }
        $auth = Auth::user();
        $upload = upload($request->file("other_file"), "/task-files/task-$request->task_id", "public");
        $info = [
            "name" => $upload["name"],
            "originale_name" => $upload["originale_name"],
            "task_id" => $request->task_id,
            "uploaded_by" =>$auth->id,
        ];
        $file = TaskFile::create($info);
        dispatch(function () use ($request, $auth) {
            $task = Task::find($request->task_id);
            \Notification::send($task->responsibles, new TaskFileAddedNotification($task, $auth));
        })->afterResponse();
        return ["success" => true, "data" => $this->_make_file_task_row($file,$auth->id), "message" =>  "Fichier bien ajouté"];
    }
    public function  delete_task_files(Request $request)
    {
        $auth =  Auth::user();
        $file = TaskFile::find($request->file_id);
        if ($file->uploaded_by != $auth->id) {
            return ["success" => false, "message" => "Vous ne pouvez pas  effectuer cette suppression"];
        }
        if ($request->input("cancel")) {
            $file->update(["deleted" => 0]);
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_file_task_row($file, $auth->id)];
        } else {
            $file->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
    public function add_new_task_checklist(Request $request)
    {
        if (!$request->new_checklist) {
            return ["success" => false, "message" =>  "Veuillez remplir le champ check-liste"];
        }
        $checklist = TaskCheckList::create(["description" => $request->new_checklist ,"user_id" => Auth::id() ,"task_id" => $request->task_id]);
        return ["success" => true, "data" => view('tasks.checklists.item', ['checklist' => $checklist])->render(), "message" =>  "Check-list bien ajouté"];
    }
    public function  mark_done_checklist(Request $request)
    {
        $checklist = TaskCheckList::find($request->checklist_id);
        $checklist->is_do = ($checklist->is_do == 1 ? 0 : 1);
        $checklist->save();
        return ["success" => true, "message" => "Mise à jour ok !", "data" => view('tasks.checklists.item', ['checklist' => $checklist])->render()];
    }
    public function  delete_checklist(Request $request)
    {
        TaskCheckList::where('id', $request->checklist_id)->update(["deleted" => 1]);
        return ["success" => true, "message" => trans("lang.success_deleted")];
    }
}
