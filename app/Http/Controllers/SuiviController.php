<?php

namespace App\Http\Controllers;

use Auth;
use stdClass;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Suivi;
use App\Models\SuiviItem;
use App\Models\SuiviType;
use App\Models\SuiviPoint;
use App\Models\SuiviVersion;
use App\Models\VersionSuivi;
use Illuminate\Http\Request;
use App\Models\SuiviItemNote;
use App\Models\CustomerFilter;
use App\Models\SuiviPauseProd;
use App\Models\SuiviTypeClient;
use App\Models\SuiviColumnCustomed;
use App\Models\SuiviVersionLevelPoint;
use App\Http\Requests\SuiviItemRequest;
use App\Models\SuiviVersionPointMontage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SaveSuiviItemNoteRequest;
use App\Http\Requests\SavePointLevelTypeProject;
use App\Models\SuiviUserParams;
use App\Notifications\UserPauseProdNotification;

class SuiviController extends Controller
{
    public function index(Request $request)
    {
       
        $tab = $request->get("tab");
        if (!in_array($tab, ["tableau", "statistique", "productivitie", "recapitulatif", "folderlist","userparams"])) {
            return redirect("/suivi/v2/projet?tab=tableau");
        }
        $fun_data = "tab_" . $tab;
        return view("suivis.index", $this->$fun_data() + ["auth"  =>  Auth::user()]);
    }
    public function tab_folderlist()
    {   
        $auth = Auth::user();
        if(!$auth->isM2pOrAdmin() && !$auth->isCp() ){
            return abort(404);
        }
        $useVuejs = true;
        $view = "folderlist";
        return compact("view","useVuejs");
    }
    public function tab_tableau()
    {
        $useVuejs = true;
        $view = "table";
        $basic_filter  = SuiviItem::createFilter();
        $options = $this->get_options();
        $columnCount = 12;
        $finished_id = SuiviItem::$FINISH;
        $paused_id = SuiviItem::$PAUSE;
        $auth = Auth::user();
        return (compact("basic_filter", "options", "columnCount", "view", "finished_id", "paused_id", "auth", "useVuejs") + SuiviItem::get_table_info());
    }
    public function tab_recapitulatif()
    {
        $years =  yearList(4,true);
        $useVuejs = true;
        $view = "recapitulatif";
        return compact("view", "useVuejs" ,"years");
    }
    public function tab_userparams()
    {
        
        $useVuejs = true;
        $view = "userparams";
        $months =  monthList(Carbon::now()->month);
        $years =  yearList();
        return compact("view", "useVuejs","months" ,"years");
    }
    public function get_total_point_prod(Request $request)
    {
        $months = ["jan", "fev", "mars", "avr", "mai", "juin", "juil", "aout", "sept", "oct", "nov", "dec"];
        $users_suivi_items =  SuiviItem::recapPoint($request->all())->get();
        foreach ($users_suivi_items  as  $user) {
            $user->traitement_grouped =  $user->suiviItems->groupBy(function ($traitement, $key) {
                return $traitement->finished_at->format('m');
            });
            $months_has = collect($user->traitement_grouped)->keys()->all();
            foreach ($months as $key => $month_name) {
                $traitement_grouped = $user->traitement_grouped->all();
                $key_month = $key + 1;
                $key_month = $key_month <= 9 ? "0" . $key_month : $key_month; // make all month to two digit
                $no_traitements = ["total_point_traitement" =>  0, "traitements" => []];
                if (in_array($key_month, $months_has)) {
                    $total_point = get_array_value($traitement_grouped, $key_month)->sum("realPointItem");
                    $user->traitement_grouped[$month_name] = ["total_point_traitement" => "$total_point", "traitements" => get_array_value($traitement_grouped, $key_month)->toArray()];
                    unset($user->traitement_grouped["$key_month"]);
                } else {
                    $user->traitement_grouped[$month_name] =  $no_traitements;
                }
            }
            unset($user->suiviItems);
        }
        return ["success" => true, "result" => $users_suivi_items->toArray() , "months" => $months];
    }
    public function get_list_point_prod(Request $request)
    {

        $months = ["jan", "fev", "mars", "avr", "mai", "juin", "juil", "aout", "sept", "oct", "nov", "dec"];
        $month = $request->month;
        $year  = $request->year ? $request->year : Carbon::now()->year;
        $date  = ["$year-$month-01" ,"$year-$month-31" ];
        $result  = SuiviItem::with(["suivi","version"])->withOut(["user","mdp"])
            ->whereDeleted(0)
            ->where("user_id" ,$request->user_id)
            ->whereBetween("finished_at",$date)
            ->finished()
            ->latest()->latest("updated_at")->latest("last_check")
            ->get();
        return ["success" => true, "result" => $result ->toArray(), "months" => $months];
    }
    public function tab_statistique()
    {
        $view = "statistique";
        $basic_filter = SuiviItem::createFilterStat();
        return compact("basic_filter", "view");
    }
    public function tab_productivitie()
    {
        // $view = "productivitie";
        $view = "productivitie2";
        // $basic_filter = SuiviItem::createFilterProd();
        $months =  monthList(Carbon::now()->month);
        $years =  yearList();
        $versions =  SuiviVersion::drop([]);
        $montages = SuiviItem::$MONTAGE;
        return compact("view",  /*"basic_filter" , */ "versions" ,"montages" ,"months" ,"years");
    }
    public function data_list(Request $request)
    {
        $data = $options =  [];
        $auth = Auth::user();
        $options = $this->get_options();
        $items  = SuiviItem::getDetails($request->all())->get();
        foreach ($items as $item) {
            $data[] = $this->_make_row($item, $auth, $options);
        }
        return ["data" => $data, "hidden_columns" => SuiviColumnCustomed::get_user_hidden_columns_array(), "pause_btn" => view('suivis.crud.pause-prod-btn', ["pause" => $this->get_last_status_prod()])->render()];
    }
    public  function _make_row(SuiviItem $item, $for_user = null, $options = [])
    {
        $clone = false;
        $row =  [
            "DT_RowId" => row_id("suivi", ($item->id)),
            "id" => $item->id,
            "detail" => view("suivis.columns.details", ["item" => $item , "clone" =>  $clone])->render(),
            "clone" => view("suivis.columns.clone", ["item" => $item, "date" => now(), "item_id" => $item->id,])->render(),
            "project_type" => view("suivis.columns.project-type", ["item" => $item, "clone" =>  $clone])->render(),
            "version" => view("suivis.columns.version", ["item" => $item, "clone" => $clone, "versions" => get_array_value($options, "versions")])->render(),
            "montage" => view("suivis.columns.montage", ["item" => $item, "clone" => $clone, "montages" => get_array_value($options, "montages")])->render(),
            "poles" => view("suivis.columns.poles", ["item" => $item, "clone" => $clone, "poles" => get_array_value($options, "poles")])->render(),
            // "point" => (isset($item->suivi) && isset($item->suivi->points)) ? $item->suivi->totalPointBase : "0.00",
            "point"  => "<span style='color:#7239EA'>". ( !$clone ? $item->realPointItem :  "0.00") ."</span>" ,
            "ref" => view("suivis.columns.ref", ["item" => $item, "clone" =>  $clone])->render(),
            "project_name" => view("suivis.columns.name-folder", ["item" => $item, "clone" =>  $clone])->render(),
            "types_client" => view("suivis.columns.types_client", ["item" => $item, "clone" =>  $clone, "types_client" => get_array_value($options, "types_client")])->render(),
            "types" => view("suivis.columns.types", ["item" => $item, "clone" =>  $clone, "types" => get_array_value($options, "types")])->render(),
            // "Difficulty" => view("suivis.columns.difficulty", ["item" => $item, "clone" =>  $clone, "levels" => $item->version->levelsPoint
            //     /** get_array_value($options, "difficulty")*/
            // ])->render(),
            "user" => view("suivis.columns.user", ["item" => $item, "clone" =>  $clone])->render(),
            "mdp" => view("suivis.columns.mdp", ["item" => $item, "clone" =>  $clone, "mdp"  => get_array_value($options, "mdp")])->render(),
            "status" => view("suivis.columns.status", ["item" => $item, "clone" => $clone, "status" => get_array_value($options, "status")])->render(),
            "duration" => view("suivis.columns.duration", ["item_id" => 0, "item" => $item, "clone" =>  $clone, "is_playing"  => ($item->status_id == SuiviItem::$IN_PROGRESS)])->render(),
            "duration_hidden" => $item->secondes ?? 0,
            "status_hidden" => view("suivis.columns.status_hidden", ["item" => $item, "clone" => $clone, "status" => get_array_value($options, "status")])->render(),
            "action" => view("suivis.columns.action", ["item_id" => $item->id, "item" => $item, "clone" => $clone])->render(),
            "extra_action" => view("suivis.columns.extra_action", ["item_id" => $item->id, "item" => $item, "clone" => $clone])->render(),
            "date" => view("suivis.columns.date", ["item" => $item,  "clone" => $clone])->render(),
            "row_details" => [
                "Type de dossier" => view("suivis.columns.category", ["item" => $item, "clone" =>  $clone, "cats" => get_array_value($options, "cats")])->render(),
                "Emplacement" => view("suivis.columns.folder-location", ["item" => $item, "clone" =>  $clone])->render(),
                "Temps de traitement estimatif" => view("suivis.columns.time-estimatif", ["item" => $item, "clone" =>  $clone, "times" => get_array_value($options, "times")])->render(),
                "Date début/Fin" => view("suivis.columns.date-info", ["item" => $item, "clone" =>  $clone])->render(),
            ]
        ];
        $auth = Auth::check() ? Auth::user() : $for_user;
        if (!$auth->isADessignator()) {
            $row["user"] =  view("suivis.columns.user", ["item" => $item, "clone" =>  $clone])->render();
        }

        return  $row;
    }
    public function add_row(Request $request)
    {

        $this->need_active_status_prod();
        $row = [];
        $auth = Auth::user();
        $clone  = true;

        if ($request->suivi_item_id) {
            $item = SuiviItem::find($request->suivi_item_id);
        } else if ($request->folder_id) {
            $item = SuiviItem::where("suivi_id", $request->folder_id)->first();
        } else {
            $item = new SuiviItem();
        }
        $item->user_id =  $auth->id;
        $item->status_id = SuiviItem::$NEW;
        $item->duration = $item->secondes = 0;
        $options = $this->get_options();
       
        $row = [
            "DT_RowId" => row_id("suivi", 0),
            "id" => $item->id,
            "detail" => view("suivis.columns.details", ["item" => $item,  "item_id" =>  0 , "clone" =>  $clone])->render(),
            "clone" => view("suivis.columns.clone", ["item" => $item, "date" => now(), "item_id" =>  0])->render(),
            "project_name" => view("suivis.columns.name-folder", ["item" => $item, "clone" =>  $clone])->render(),
            "types_client" => view("suivis.columns.types_client", ["item" => $item, "clone" =>  $clone, "types_client" => get_array_value($options, "types_client")])->render(),
            "ref" => view("suivis.columns.ref", ["item" => $item, "clone" =>  $clone])->render(),
            "project_type" => view("suivis.columns.project-type", ["item" => $item, "clone" =>  $clone])->render(),
            // "Difficulty" => view("suivis.columns.difficulty", ["item" => $item, "clone" =>  $clone, "levels" => $item->version ? $item->version->levelsPoint : []
            //     /** get_array_value($options, "difficulty") */
            // ])->render(),
            "poles" => view("suivis.columns.poles", ["item" => $item, "clone" => $clone, "poles" => get_array_value($options, "poles")])->render(),
            // "point" => (isset($item->suivi) && isset($item->suivi->points)) ? $item->suivi->totalPointBase : "0.00",
            "point"  => "<span style='color:#7239EA'>". ( !$clone ? $item->realPointItem :  "0.00") ."</span>" ,
            "version" => view("suivis.columns.version", ["item" => $item, "clone" => $clone, "versions" => get_array_value($options, "versions")])->render(),
            "mdp" => view("suivis.columns.mdp", ["item" => $item, "clone" =>  $clone, "mdp"  => get_array_value($options, "mdp")])->render(),
            "montage" => view("suivis.columns.montage", ["item" => $item, "clone" => $clone, "montages" => get_array_value($options, "montages")])->render(),
            "status" => view("suivis.columns.status", ["item" => $item, "clone" => $clone, "status" => get_array_value($options, "status")])->render(),
            "duration" => view("suivis.columns.duration", ["item_id" => 0, "item" => $item, "clone" =>  $clone, "is_playing" => (($item->status_id == SuiviItem::$IN_PROGRESS))])->render(),
            "types" => view("suivis.columns.types", ["item" => $item, "clone" =>  $clone, "types" => get_array_value($options, "types")])->render(),
            "duration_hidden" => 0,
            "status_hidden" => view("suivis.columns.status_hidden", ["item" => $item, "clone" => $clone, "status" => get_array_value($options, "status")])->render(),
            "action" => view("suivis.columns.action", ["item_id" => 0, "item" => $item, "clone" =>  $clone])->render(),
            "extra_action" => view("suivis.columns.extra_action", ["item_id" => $item->id ?? 0, "item" => $item, "clone" => $clone])->render(),
            "date" => view("suivis.columns.date", ["item" => $item, "clone" => $clone,])->render(),
            "row_details" => [
                "Type de dossier" => view("suivis.columns.category", ["item" => $item, "clone" =>  $clone, "cats" => get_array_value($options, "cats")])->render(),
                "Emplacement" => view("suivis.columns.folder-location", ["item" => $item, "clone" =>  $clone])->render(),
                "Temps de traitement estimatif" => view("suivis.columns.time-estimatif", ["item" => $item, "clone" =>  $clone, "times" => get_array_value($options, "times")])->render(),
                "Date début/Fin" => view("suivis.columns.date-info", ["item" => $item, "clone" =>  $clone])->render(),
            ]
        ];

        if (!$auth->isADessignator()) {
            $row["user"] =   view("suivis.columns.user", ["item" => $item, "clone" =>  $clone])->render();
        }
        return ["success" => true, "message" => "Ligne bien ajoutée", "item" => $row, "is_clone" => $clone, "row_id" =>  0];
    }
    public function get_options($params = [])
    {
        $montages = SuiviItem::getMontage();
        $poles = SuiviItem::getPole();
        $status = SuiviItem::getStatus();
        $versions = SuiviVersion::getVersions()->get();
        $mdp = SuiviItem::getMdp();
        $cats = SuiviItem::getTypeFolder();
        $types_client = SuiviItem::getClientType();
        $types = SuiviType::dropdown(); // project type
        $times = SuiviItem::getTimeEstimatif();
        return compact("status", "versions", "montages", "cats", "poles", "mdp", "types_client", "times", "types");
    }
    public function save_row(SuiviItemRequest $request)
    {
        
        $this->need_active_status_prod();
        $auth = Auth::user();
        $user = User::withOut(['userJob'])->whereDeleted(0)->find(($request->user_id ? $request->user_id :  $auth->id));

        $item_id =  (int) $request->item_id;
        $clon_of =  (int) $request->clon_of;
        $id = 0;
        if ($item_id) {
            $id = $item_id;
        }
        if ($clon_of) {
            $id = $clon_of;
        }
        $item = SuiviItem::find($id);
        $options = $this->get_options();

        if (!$clon_of) {
            $points = [];
            $request_types =  is_array($request->types) ? $request->types : [$request->types];
            foreach ($request_types as $project_type_id) {
                $point_per_type  = SuiviPoint::where("client_type_id", $request->type_client)
                                            ->where("project_type_id", $project_type_id)
                                            ->where("pole", $request->poles)
                                            ->whereDeleted(0)->latest()->first();
                if ($point_per_type) {
                    $points[] = $point_per_type->id;
                }
            }
            if (isset($item->id)) {
                $this->can_do_this($item);
                /** Can do other folder */
                if ($user->isADessignator() &&  in_array($request->status_id, [SuiviItem::$IN_PROGRESS])) {
                    $this->can_do_other_traitement($user);
                }
                /** Do a calcul duration */
                if (in_array($request->status_id, [ SuiviItem::$FINISH, SuiviItem::$PAUSE, /**SuiviItem::$VERIFIED ,*/ SuiviItem::$IN_PROGRESS])
                    /** && ($request->status_id != $item->status_id) */
                ) {
                    $update_data = ["duration" => $this->calcul_duration($item)];
                }
                /** Set only finish if it is already paused */
                /** dont do a calcul duration */
                if (in_array($request->status_id, [SuiviItem::$FINISH]) && $item->status_id == SuiviItem::$PAUSE) {
                    $update_data = [];
                }
                if (in_array($request->status_id, [ SuiviItem::$FINISH,/**SuiviItem::$VERIFIED */ ])) {
                    $update_data["disabled"] = 1;
                    $update_data["finished_at"] = now()->format("Y-m-d h:i:s");
                    dispatch(function () use ($request, $item) {
                        $this->set_duplicates_item_to_finished($request, $item);
                    })->afterResponse();
                }
                $update_data["last_check"] = now();
                $data = $request->all();
                if ($item->suivi_id) {
                    $suivi = Suivi::find($item->suivi_id);
                    dispatch(function () use ($suivi, $data, $request, $points) {
                        if ($request->level_id != $suivi->level_id) {
                            unset($data["level_id"]);
                        }
                        $suivi->update($data);
                        // $suivi->types()->sync($request->types);
                        $suivi->points()->sync($points);
                    })->afterResponse();
                    if ($request->level_id != $suivi->level_id) {
                        $data["level_id"] = $request->level_id;
                    }
                    $item =  SuiviItem::updateOrCreate(["id" => $item->id], $data +  $update_data);
                } else {
                    $data["creator_id"] = $auth->id;
                    $suivi = Suivi::create($data);
                    dispatch(function () use ($request, $suivi, $points) {
                        // $suivi->types()->sync($request->types);
                        $suivi->points()->sync($points);
                    })->afterResponse();
                    $item =  SuiviItem::updateOrCreate(["id" => $item->id], $data + ["suivi_id"  => $suivi->id] +  $update_data);
                }
                $item->refresh();
                $message =  'Mise à jour bien effectuée';
            } else {
                /** Can do other folder */
                if ($user->isADessignator() &&  in_array($request->status_id, [SuiviItem::$IN_PROGRESS])) {
                    $this->can_do_other_traitement($user);
                }
                /**New item */
                $suivi = Suivi::create($request->only(["folder_name", "project_type", "ref", "level_id"]) +  ["creator_id" => $auth->id]);
                $item =  $suivi->items()->create($request->all() + ["user_id" => $user->id]);
                dispatch(function () use ($request, $suivi, $points) {
                    // $suivi->types()->sync($request->types);
                    $suivi->points()->sync($points);
                })->afterResponse();
                $message =  'Ajout bien effectuée';
            }
        } else {
            if ($user->isADessignator() &&  in_array($request->status_id, [SuiviItem::$IN_PROGRESS])) {
                $this->can_do_other_traitement($user);
            }
            $message =  'Ajout bien effectuée';
            $item = $this->clone_item($request, $user, $item, $options);
        }
        return ["success" => true, "message" =>   $message, "row_id" => row_id("suivi", ($item_id ??  0)), "item" => $this->_make_row($item, $user, $options)];
    }
    private function set_duplicates_item_to_finished($request, $item)
    {
        $duplicates = SuiviItem::where("suivi_id", $item->suivi_id)
            ->where("version_id", $request->version_id)
            ->where("montage", $request->montage)
            ->where("id", "<>", $item->id)
            ->where("status_id", "<>", SuiviItem::$FINISH)->get();
        if ($duplicates->isEmpty()) {
            return;
        }
        foreach ($duplicates as $item) {
            $item->update(["finished_at" => now()->format("Y-m-d h:i:s"),"status_id" => SuiviItem::$FINISH,"duration" => $this->calcul_duration($item)]);
        }
    }
    private function need_active_status_prod()
    {
        $auth = Auth::user();
        if ($auth->isM2pOrAdmin() || $auth->isCp()) {
            return;
        }
        $last = $this->get_last_status_prod();
        if (!$last || $last->status == "pause") {
            die(json_encode(["success" => false, "message" => "Veuillez changer votre statut en « En traitement de dossier »"]));
        }
    }
    private function get_last_status_prod()
    {
        return SuiviPauseProd::where("user_id", "=", Auth::id())->latest()->first();
    }

    private function clone_item($request, $auth, $source, $options)
    {
        $data = $request->all();
        $data["creator_id"] = $auth->id;
        return  SuiviItem::create($data + ["user_id" => $auth->id, "suivi_id"  => $source->suivi_id]);
    }
    public function calcul_duration($item)
    {
        return $item->secondes;
    }
    public function delete_row_confiramtion_modal(Request $request)
    {
        $item = SuiviItem::find($request->item_id) ?? new SuiviItem();
        return view("suivis.crud.delete-row", ["item" =>  $item, "row_id" => row_id("suivi", $item->id ?? 0), "auth" => Auth::user()]);
    }
    public function delete_row(Request $request)
    {

        $suiviItem = SuiviItem::find($request->item_id);
        if (!$suiviItem) {
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
        if (!$suiviItem->can_delete_row()) {
            return ["success" => false, "can_not_undo" => true, "message" => 'Vous ne pouvez pas supprimer ce ligne dossier traité . Acces non autorisé !'];
        }
        if ($suiviItem) {
            if ($request->input("cancel")) {
                dispatch(function () use ($suiviItem) {
                    $suiviItem->update(["deleted" => 0]);
                })->afterResponse();
                return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row($suiviItem, Auth::user(), $this->get_options())];
            } else {
                dispatch(function () use ($suiviItem) {
                    $suiviItem->update(["deleted" => 1]);
                })->afterResponse();
                return ["success" => true, "message" => trans("lang.success_deleted")];
            }
        } else {
            return ["success" => true, "can_not_undo" => true, "message" => trans("lang.success_deleted")];
        }
    }

    public function  can_do_other_traitement($user, $message = "")
    {
        $folder_in_progress = SuiviItem::where("user_id", $user->id)->where("status_id", SuiviItem::$IN_PROGRESS)->whereDeleted(0)->count();
        if ($folder_in_progress > 1) {
            $message =  $message  ?  $message : 'Vous avez 2 ou plus de dossiers « En cours » de statut. <br> Mettez  en « Pause » ou « Terminer » les autres dossiers avant de mettre   « En cours » une autre! ';
            die(json_encode(["success" => false, "message" =>  $message]));
        }
    }
    private function  can_do_this(SuiviItem $item)
    {
        if ($item->deleted) {
            die(json_encode(["success" => false, "message" => 'Cette ligne est déja supprimer ailleur !']));
        }
        if (!$item->can_update_row()) {
            die(json_encode(["success" => false, "message" => 'Vous ne pouvez pas modifer ce ligne dossier. Acces non autorisé!']));
        }
    }
    public function search_folder(Request $request)
    {
        return  Suivi::search_folder($request->term);
    }
   
    public function folder_list(Request $request)
    {
        $per_page = 5;
        $skip = $request->skip;
        $term = $request->term;
        $hasAnotherData = false;

        $query =  Suivi::withOut(["points"])->whereDeleted(0)->latest();
        if ($term) {
            $data = $query->where(function ($query) use ($term) {
                if (!in_array(strtolower($term), ["tous", "all" ,"toutes","tout","listes","list", "***"])) {
                    $query->where("folder_name", 'like', '%' . $term . '%');
                    $query->orWhere("ref", 'like', '%' . $term . '%');
                }
            })->get();
        }else{
            $skip = $skip == 1 ? 0 : $skip;  
            if ($skip) {
                $query->skip($skip);
            }
            $data = $query->limit($per_page + 1)->get();
            $skip = $per_page +  $skip;
            if ($data->count() >= $per_page + 1) {
                $hasAnotherData = true;
                $data = $data->forget($data->count() - 1);
            }
        }
        
        return ["success" => true, "result" => $data, 'hasAnotherData' => $hasAnotherData, 'currentPage' => $skip];

    }
    public function delete_folder(Request $request)
    {
        Suivi::where("id" ,$request->folder_id )->update(["deleted"=> 1]);
        return ["success" => true, "message" => trans("lang.success_deleted")];
    }
    public function search_user(Request $request)
    {
        $data = [];
        $users = User::with(["userJob.job"])->where(function ($query) use ($request) {
            if (!in_array(strtolower($request->term), ["tous", "all", "toutes", "tout"])) {
                $query->where("name", 'like', '%' . $request->term . '%');
                $query->orWhere("firstname", 'like', '%' . $request->term . '%');
                $query->orWhere("email", 'like', '%' . $request->term . '%');
                $query->orWhere("registration_number", 'like', '%' . $request->term . '%');
            }
        })->whereDeleted(0)->get();
        foreach ($users as $user) {
            $sortJob = ($user->userJob && $user->userJob->job) ?  " - " . $user->userJob->job->name  : "";
            $data[] = ["id" => $user->id, "text" => $user->sortname, "img" => $user->avatarUrl, "info" => "(" . $user->registration_number   . $sortJob . ")"];
        }
        return ["results" => $data];
    }
    public function custom_filter_modal(Request $request)
    {
        return view("suivis.crud.customer-filter", ["options" => $this->get_options(), "modal" => true]);
    }
    public function save_custom_filter(Request $request)
    {
        if (!$request->name_filter) {
            return ["success" => false, "message" => 'Veuillez nommé le mon du filtre '];
        }
        $filters = $request->only("users", "suivis", "types", "versions", "montages", "status", "poles");
        $l = CustomerFilter::create(["name_filter" => $request->name_filter, "creator" => Auth::id(), "filters" => count($filters) ? serialize($filters) : null]);
        return ["success" => true, "message" => 'Ajout bien effectué', "data" => $this->_make_row_custom_filter($l)];
    }

    public function custom_filter_data_list()
    {
        $data = [];
        $list = Auth::user()->customFilter()->get();
        foreach ($list as $l) {
            $data[] = $this->_make_row_custom_filter($l);
        }
        return ["data" =>  $data];
    }

    private function _make_row_custom_filter(CustomerFilter $l)
    {
        return [
            "id" => $l->id,
            "name" => $l->name_filter,
            "filters" => $this->filter_details(unserialize($l->filters)),
            "actions" => js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/suivi/custom-filter-data-delete/$l->id"), "class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"]),
        ];
    }
    public function delete_custom_filter(Request $request, CustomerFilter $customerFilter)
    {
        if ($request->input("cancel")) {
            $customerFilter->update(["deleted" => 0]);
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row_custom_filter($customerFilter)];
        } else {
            $customerFilter->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
    public function more_detail_item(Request $request)
    {
        $item = SuiviItem::find($request->item_id);
        $clients_type =  to_dropdown(SuiviTypeClient::dropdown(), "id", "name");
        return [
            "view" => view("suivis.columns.details-column.details", ["item" => $item, "cats" =>  SuiviItem::getTypeFolder(), "clients_type" => $clients_type])->render(),
            // "info" => `<h3>{$item->suivi->folder_name}<small class="text-white opacity-50 fs-7 fw-semibold pt-1">Réference :  {$item->suivi->ref}z </small></h3>`
        ];
    }
    
    public function save_more_detail_item(Request $request)
    {
        $item = SuiviItem::withOut(["suivi"])->find($request->item_id);
        $suivi = Suivi::find($item->suivi_id);

        $suivi->folder_location = $request->folder_location;
        $suivi->category = $request->category;

        $item->times_estimated = $request->times_estimated;
        dispatch(function () use ($suivi, $item) {
            $item->save();
            $suivi->save();
        })->afterResponse();
        return ["success" => true, "data" => "", "message" => trans("lang.success_record")];
    }
    /** Save note   qualité */
    public function save_note( SaveSuiviItemNoteRequest  $request)
    {
        $data = $request->only("note","suivi_item_id");
        $data["creator_id"] = Auth::id();
        $note = SuiviItemNote::create($data);
        return ["success" => true, "data" => $note, "message" => trans("lang.success_record")];
    }
    public function suivi_item_note_list(Request $request)
    {
        $auth = Auth::user();
        $data = [];
        $notes = SuiviItemNote::with(["creator"])->where("suivi_item_id",$request->suivi_item_id)->whereDeleted(0)->latest()->get();
        foreach ($notes as $note) {
            $data[] = $this->_row_suivi_item_note_list($note ,  $auth);
        }
        return ["success" => true, "data" => $data];
    }
    private function _row_suivi_item_note_list(SuiviItemNote $note ,$auth)
    {
        $actions = "";
        if(!isset($note->creator)){
            $note->load(["creator"]);
        }
        if($note->creator->id == $auth->id  ||  $auth->isM2pOrAdmin() ||  $auth->isCp()){
            $actions .= js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/suivi/delete/note/$note->id"), "class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"]);
        }
        return [
            "note" =>$note->note ,
            "creator" => view("suivis.columns.user-avatar", ["user" => $note->creator])->render() ." " ,
            "date" => convert_to_real_time_humains($note->created_at),
            "actions" => $actions ,
        ];
    }

    public function suivi_item_note_delete(SuiviItemNote $note,Request  $request)
    {   
        if ($request->input("cancel")) {
            $note->update(["deleted" => 0]);
            return ["success" => true, "message" => trans("lang.success_canceled"),"data" => $this->_row_suivi_item_note_list($note,Auth::user())];
        } else {
            $note->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
    private  function filter_details($data)
    {
        $strings = "";
        /**The users */
        $users = get_array_value($data, "users");
        if ($users) {
            $users = User::findMany($users);
            $strings .=  " - <u>Personnes</u> : " . $users->implode("sortname", ", ") . "<br>";
        }
        /**The folders */
        $suivis = get_array_value($data, "suivis");
        if ($suivis) {
            $suivis = Suivi::findMany($suivis);
            $strings .=  "  - <u>Dossiers</u> :  " .  $suivis->implode("folder_name", ", ") . "<br>";
        }
        $versions = get_array_value($data, "versions");
        if ($versions) {
            $versions = VersionSuivi::findMany($versions);
            $strings .=  "  - <u>Versions</u> :  " . $versions->implode("title", ", ") . "<br>";
        }
        $montages = get_array_value($data, "montages");
        if ($montages) {
            $montages = collect(SuiviItem::$MONTAGE)->whereIn("value", $montages);
            $strings .=  "  - <u> Montages</u> :  " . $montages->implode("text", ", ") . "<br>";
        }
        $status = get_array_value($data, "status");
        if ($status) {
            $status = collect(SuiviItem::$STATUS)->whereIn("value", $status);
            $strings .=  "  - <u> Status</u> :  " . $status->implode("text", ", ") . "<br>";
        }
        $poles = get_array_value($data, "poles");
        if ($poles) {
            $poles = collect(SuiviItem::$POLES)->whereIn("value", $poles);
            $strings .=  "  - <u> Pôles</u> :  " . $poles->implode("text", ", ") . "<br>";
        }
        return $strings ? $strings : ' - <u> Rien</u>';
    }

    public function version_modal()
    {
        // $option =  $this->get_options()
        $versions = VersionSuivi::whereDeleted(0)->get();
        return view("suivis.crud.version-modal", ["versions" => $versions]);
    }

    public function save_version(Request $request)
    {
        $point = [];
        // $rules = [];
        if (!$request->version_suivi) {
            return ["success" => false, "message" => ' Le mon du verison est obligatoire',];
        }
        if (!$request->poles) {
            return ["success" => false, "message" => 'Veuillez attacher un pôle !',];
        }
        // if($request->version_id_base || $request->percentage  ){
        //     $rules= ['percentage' => "required" , "version_id_base" => "required" ,'point' => "nullable"];
        // }
        // $validator = Validator::make($request->all(),$rules);
        // if($validator->fails()){
        //     return ["success" => false, "validation" => true,  "message" => $validator->errors()->all()];
        // }
        $poles = collect(SuiviItem::$POLES);
        $belogns = "";
        foreach ($request->poles as $pole) {
            if ($poles->where("value", $pole)->count()) {
                $belogns = $belogns ? ($belogns . "," . $pole) : $pole;
            }
        }
        // if($request->version_id_base || $request->percentage ){
        //     $percentage = str_replace([",","%"],[".",""],$request->percentage);
        //     $point= ['percentage' => $percentage , "version_id_base" => $request->version_id_base , 'point' => null  ];
        // }else{
        //     $point= ['percentage' => null  , "version_id_base" => null  , 'point' => $request->point ];
        // }
        $version = VersionSuivi::create(["title" => $request->version_suivi, "belongs" =>  $belogns,  "creator_id" => Auth::id()] +  $point);
        return ["success" => true, "message" => 'Ajout bien effectué', "data" => $this->_make_row_version($version)];
    }
    public function data_list_version()
    {
        $data = [];
        $versions = VersionSuivi::with(["creator", "base_calcul"])->whereDeleted(0)->orderBy("id", "DESC")->get();
        foreach ($versions  as $version) {
            $data[] = $this->_make_row_version($version);
        }
        return ["data" =>  $data];
    }
    private function _make_row_version(VersionSuivi $version)
    {
        return [
            "title" => $version->title,
            "belongs" => $version->belongs,
            "creator" => $version->creator_id ? $version->creator->sortname :  "",
            "point" => $version->base_calcul ? $version->base_calcul->title . " ($version->percentage %)" : ($version->point ?? ""),
            //"action" => ($version->creator_id == Auth::id()) ? js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/suivi/delete/version/$version->id"), "class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"])    : "",
            "action" => js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/suivi/delete/version/$version->id"), "class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"]),
        ];
    }
    public function delete_version(Request $request, VersionSuivi $version)
    {
        if ($version->creator_id != Auth::id()) {
            return ["success" => true, "message" => "Action non autorisé"];
        }
        if ($request->input("cancel")) {
            dispatch(function () use ($version) {
                $version->update(["deleted" => 0]);
            })->afterResponse();
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row_version($version)];
        } else {
            dispatch(function () use ($version) {
                $version->update(["deleted" => 1]);
            })->afterResponse();
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }

    public function type_modal()
    {
        return view("suivis.crud.type-modal",);
    }
    public function level_modal()
    {
        $versions = SuiviVersion::getVersions()->get();
        return view("suivis.crud.level-modal", ["versions" => $versions]);
    }
    /** Point form an list per Type client */
    public function point_modal()
    {
        $client_types = SuiviTypeClient::dropdown();
        $project_types = SuiviType::dropdown();
        $levels = SuiviItem::getLevels();
        $poles = SuiviItem::getPole();
        $montages = SuiviItem::getMontage();
        $versions = SuiviVersion::whereDeleted(0)->get();
        $versions_base = SuiviTypeClient::with(["project_types"])->get();
        return view("suivis.crud.point-modal", ["client_types" => $client_types, "project_types" => $project_types, "levels" => $levels, "versions" => $versions , "versions_base" => $versions_base , "montages" => $montages ,"poles"  => $poles]);
    }

    // Save point and niveau by type client and type project 
    public function save_point_level(SavePointLevelTypeProject $request)
    {
        $data = $request->except("_token");
        $data["point"] = str_replace([","," "],["." , ""], $request->point);
        $data["point_sup"] = str_replace([","," "],["." , ""], $request->point_sup);
        $point = SuiviPoint::create($data);
        return ["success" => true, "message" => trans("lang.success_record")];
    }
    /** Old concept  */
    // public function point_data(Request $request)
    // {
    //     $data = [];
    //     $client_type = SuiviTypeClient::with(["project_types"])->find($request->client_type_id);
    //     $project_types =  $client_type->project_types ??  [];
        
    //     foreach ($project_types as $type) {
    //         $data[] = [
    //             "client_types" =>  $client_type->name,
    //             "project_types" => $type->name,
    //             "version" =>   $type->pivot->version_id ? SuiviVersion::find($type->pivot->version_id)->title  : "-",
    //             "niveau" =>  $type->pivot->niveau,
    //             "point" => $type->pivot->point,
    //             "point_sup" =>  $type->pivot->point_sup,
    //             "created_at" =>  Carbon::parse($type->pivot->created_at)->format("d-m-Y"),
    //             "pole" =>  $type->pivot->pole,
    //             "action" =>  js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/suivi/delete/point"), "data-post-point_id" => $type->pivot->id ,"class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"]),
    //         ];
    //     }
    //     return ["data" =>  $data];
    // } 
     /** End of old concept  */
    public function point_data(Request $request)
    {
        $data = [];
        $points =   SuiviPoint::with(["client_type" , "project_type" , "version" , ])->where("client_type_id",$request->client_type_id )->where("suivi_points.deleted" , 0)->get();
        foreach ($points as $point) {
            $data[] = [
                "client_types" =>  $point->client_type_id ? $point->client_type->name : "-" ,
                "project_types" => $point->project_type_id ?   $point->project_type->name :  "-",
                "version" =>   $point->version_id ? $point->version->title : "-",
                "niveau" =>  $point->niveau,
                "point" => $point->point,
                "point_sup" =>  $point->point_sup,
                "created_at" =>  Carbon::parse($point->created_at)->format("d-m-Y"),
                "pole" =>  $point->pole,
                "action" =>  js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/suivi/delete/point"), "data-post-point_id" => $point->id ,"class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"]),
            ];
        }
        return ["data" =>  $data];
    }
    public function point_delete(Request $request)
    {
        SuiviPoint::where("id", $request->point_id)->update(["deleted" =>  1 ]);
        return ["success" => true , "message" =>  trans("lang.success_deleted")]; 
    }
    public function save_other_version_point(Request $request)
    {
  
        $data = [];
        $rules = ['version_id_of_calcul' => "required" , "montage" => "nullable"];
        if ($request->version_id_base || $request->percentage) {
            $rules =  $rules + ['percentage' => "required|numeric", "version_id_base" => "required", 'point' => "nullable"];
        } else {
            $rules =  $rules + ['point' => "required|numeric" ,];
        }
        $validator = Validator::make($request->all(), $rules, ["percentage.numeric" => "Le champ pourcentage doit contenir un nombre entier ou decimal uniquement"]);
        if ($validator->fails()) {
            return ["success" => false, "validation" => true,  "message" => $validator->errors()->all()];
        }
        $data = $request->only("percentage", "version_id_base", "point","version_id_of_calcul","montage",'pole');
        $data["point"] = str_replace([","," "],["." , ""], $request->point);
        $data["percentage"] = str_replace([","," " , "%"],["." , "", ""], $request->point);
        if ($request->montage) {
            $data["version_id"] = $request->version_id_of_calcul;
            unset($data["version_id_of_calcul"]);
            SuiviVersionPointMontage::updateOrCreate(["version_id" => $request->version_id_of_calcul ,"version_id_base" =>$request->version_id_base, "montage" =>  $request->montage,"pole" =>$request->pole], $data);
        }else{
            SuiviVersion::where("id", $request->version_id_of_calcul)->update($data);
        }
        return ["success" => true, "message" => trans("lang.success_record")];
    }
    public function data_other_version_point(Request $request)
    {
        $data = [];
        $verisons = SuiviVersion::with(['base_calcul:id,title,deleted'])->whereNotNull("point")->orWhereNotNull("version_id_base")->whereDeleted(0)->get();
        $points_version_montage = SuiviVersionPointMontage::with(["version","base_calcul"])->whereDeleted(0)->get();
       
        foreach ($verisons as $verison) {
            $data[] = [
                "version_name" =>  $verison->title,
                "belongs" => $verison->belongs,
                "montage" => $verison->montage ?? "-",
                "point" => $verison->point ?  $verison->point : ( $verison->percentage ? ($verison->percentage  . "% de " . $verison->base_calcul->title) : "-"),
                // "action" => "editer" ,
            ];
        }
        foreach ($points_version_montage as $point_version_montage) {
            $data[] = [
                "version_name" =>  $point_version_montage->version->title,
                "belongs" => $point_version_montage->version->belongs,
                "montage" => $point_version_montage->montage ?? "-",
                "point" => $point_version_montage->point != null ?  $point_version_montage->point : ($point_version_montage->percentage  . "% de " . $point_version_montage->base_calcul->title ),
                // "action" => "editer" ,
            ];
        }
        return ["data" =>  $data];;
    }

    public function load_level_point(Request $request)
    {
        $level_points = SuiviVersionLevelPoint::whereDeleted(0)->where("version_id", $request->version_id)->get();
        return view("suivis.crud.level-point-list", ["level_points" => $level_points]);
    }
    public function save_level_point(Request $request)
    {
        $data = [];
        $version_id = $request->version_id;
        $difficulties = $request->difficulties;
        $points = $request->points;
        for ($i = 0; $i < count($difficulties); $i++) {
            $this->validation_level_and_point($difficulties[$i], $points[$i]);
            if (!is_null($difficulties[$i]) && !is_null($points[$i])) {
                $data[] = ["version_id" =>  $version_id, "level" => $difficulties[$i], "point" => $points[$i]];
            }
        }
        // SuiviVersionLevelPoint::where("version_id","=",$version_id)->delete();
        SuiviVersionLevelPoint::where("version_id", "=", $version_id)->update(["deleted" => 1]);
        SuiviVersionLevelPoint::insert($data);
        $level_points = SuiviVersionLevelPoint::whereDeleted(0)->where("version_id", $request->version_id)->get();
        return ["success" => true, "data" => view("suivis.crud.level-point-list", ["level_points" => $level_points])->render(), "message" => 'Ajout bien effectué'];
    }

    public function validation_level_and_point($level, $point)
    {
        if (is_null($level) && !is_null($point)) {
            die(json_encode(["success" => false, "message" => 'Les points ne peut pas etre vide']));
        }
        if (is_null($level) && !is_null($point)) {
            die(json_encode(["success" => false, "message" => 'Les niveaux ne peut pas etre vide']));
        }
    }
    public function save_type_suivi(Request $request)
    {
        if (!$request->type_suivi) {
            return ["success" => false, "message" => ' Le type dossier est obligatoire',];
        }
        $type = SuiviType::create(["name" => $request->type_suivi,  "user_id" => Auth::id()]);
        return ["success" => true, "message" => 'Ajout bien effectué', "data" => $this->_make_row_type($type)];
    }

    public function data_list_type()
    {
        $data = [];
        $types = SuiviType::with(["creator"])->whereDeleted(0)->latest()->get();
        foreach ($types  as $type) {
            $data[] = $this->_make_row_type($type);
        }
        return ["data" =>  $data];
    }
    private function _make_row_type(SuiviType $type)
    {
        return [
            "name" => $type->name,
            "creator" => $type->user_id ? $type->creator->sortname :  "",
            "action" => ($type->user_id == Auth::id())
                ? js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/suivi/delete/type/$type->id"), "class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"])
                : "",
        ];
    }

    public function delete_type(Request $request, SuiviType $type)
    {
        if ($type->user_id != Auth::id()) {
            return ["success" => true, "message" => "Action non autorisé"];
        }
        if ($request->input("cancel")) {
            dispatch(function () use ($type) {
                $type->update(["deleted" => 0]);
            })->afterResponse();

            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row_type($type)];
        } else {
            dispatch(function () use ($type) {
                $type->update(["deleted" => 1]);
            })->afterResponse();
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }

    /*load Stat data*/
    public function load_stat(Request $request)
    {
        $dataset = $labels = [];
        $i = 1;
        $versions = VersionSuivi::getDetail($request->all())->get();
        $function_foreign = explode("|", ($request->stat_on ?? "Status|status_id"));
        $function = "get" . "$function_foreign[0]";
        $on_in = SuiviItem::$function();
        foreach ($on_in as $on) {
            $data = [];
            foreach ($versions as $version) {
                $i == 1 ? ($labels[] = $version->title) :  null;
                $data[] = $version->items->where($function_foreign[1], get_array_value($on, "value"))->count();
            }
            $dataset[]  = ["label" => get_array_value($on, "text"), "data" =>  $data, "backgroundColor" => get_array_value($on, "color"), "stack" => "Stack-" . get_array_value($on, "value")];
            $i++;
        }
        return ["success" =>  true, "dataset" => $dataset, "labels" => $labels];
    }
    public function load_prod(Request $request)
    {
        
        $data = $models_groupable = $columns = $options =  [];
        $real_columns = [
            // ["value" => "id", "text" => "#", "class" => "text-left " ,  ],
            ["value" => "user_resgistraion", "text" => "#", "class" => "text-left " ,  "fixed" => true,],
            ["value" => "user", "text" => "Nom", "class" => "text-left" , "fixed" => true, "width" => 200],
            ["value" => "days_work_nb", "text" => "Jour de travail", "class" => "text-left" ,  "width" => 120],
            ["value" => "days_work_percent", "text" => " % Jour de travail", "class" => "text-left" ,  "width" => 120],

            ["value" => "time_plus_or_minus", "text" => "Heure+/-", "class" => "text-left"],
            ["value" => "days_plus_or_minus", "text" => "Jour+/-", "class" => "text-left"],

            ["value" => "days_work_nb_dessi", "text" => "Jour Dessi", "class" => "text-left"],
            ["value" => "days_work_percent_dessi", "text" => "% Jour Dessi", "class" => "text-left", "columnColor" => null],
            ["value" => "seuil_point", "text" => "Seuil journalier",  "class" => "text-left" ,"width" => 150 ],
            ["value" => "hours_works", "text" => "Heure journalier théorique",  "class" => "text-left" ,"width" => 200 ],
            ["value" => "sum_pause", "text" => "Pause", "class" => "text-left" ],

            ["value" => "average", "text" => "Moyenne/100", "class" => "text-left"],
            ["value" => "note_prod", "text" => "Note prod", "class" => "text-left" ,"sortable" => true ,"width" => 100],
            ["value" => "sum_note_quality", "text" => "Note qualité", "class" => "text-left" ,"sortable" => true ,"width" => 120 ],
            ["value" => "point_prod", "text" => "Point prod", "class" => "text-left" ,"sortable" => true ,"width" => 100 ],
            ["value" => "point_per_day", "text" => "Point jour.",  "class" => "text-left" ],
           
            // ["data" => "rating_productivitie", "title" => "Note Prod./10", "class" => "text-left"],
            // ["data" => "rating_qualification", "title" => "Note Qual./10", "class" => "text-left"],
            // ["data" => "sum_difficulty", "title" => "Difficulté", "class" => "text-left"],
        ];
        $options = $request->all();
        $version_ids = get_array_value($options, "version_ids", []);
        if ($version_ids) {
            $models_groupable["versions"] = SuiviVersion::findMany($version_ids, ["id", "title"])->toArray();
        }
        $montage_ids = get_array_value($options, "montage_ids");
        if ($montage_ids) {
            $models_groupable["montages"] = collect(SuiviItem::$MONTAGE)->whereIn("value", $montage_ids);
        }
        $folder_ids = get_array_value($options, "folder_ids");
        if ($folder_ids) {
            $models_groupable["folders"] = Suivi::findMany($folder_ids, ["id", "folder_name as title"])->toArray();
        }

        $nb_days_in_month = getNumberDayInMontWithoutWeekends(get_array_value($options, "month", Carbon::now()->month), get_array_value($options, "year", Carbon::now()->year));
        $results = SuiviItem::productities($options)->get();
        foreach ($results as $user) {
            if ($version_ids) {
                $user->grouped_versions = $user->suiviItems->groupBy("version_id")->all();
            }
            if ($montage_ids) {
                $user->grouped_montages = $user->suiviItems->groupBy("montage")->all();
            }
            if ($folder_ids) {
                $user->grouped_folders = $user->suiviItems->groupBy("suivi_id")->all();
            }
            $data[] = $this->_make_data_list($user, $models_groupable, $nb_days_in_month, $options, $columns, $real_columns);
        }
        return ["success" => true, "headers" => $real_columns, "items" => $data];
    }
    private function  _make_data_list($user, $groupables, $nb_days_in_month, $options, &$columns = [], &$real_columns = [])
    {
        $row = [];
        $row["user_id"] = $user->id;
        $row["user_resgistraion"] = $user->registration_number;
        $row["user"] = view("suivis.columns.user-avatar", ["user" => $user])->render();
        $row["sum_pause"] =  $user->sum_pause ? seconds_to_dhms($user->sum_pause) : "00:00:00:00";

        $need_work_info = $this->calcul_day_need_working($user->dayOffs, $nb_days_in_month, $options);
        $row["days_work_nb"] = $need_work_info->html;
        $row["days_work_percent"] =  $this->calcul_percent_day_need_working($need_work_info->value, $nb_days_in_month);
        
        $row["hours_work_per_day"] = 8;
        $row["seuil_point"] ="0.00";

        if( isset($user->suiviPramsSession) && isset( $user->suiviPramsSession->seuil_point) &&  $user->suiviPramsSession->seuil_point){
            $row["seuil_point"] =  round($user->suiviPramsSession->seuil_point,2);
        }
        if(isset($user->suiviPramsSession) && isset( $user->suiviPramsSession->hours_works) &&  $user->suiviPramsSession->hours_works){
            $row["hours_works"] = round($user->suiviPramsSession->hours_works,2);
        }else{
            $row["hours_works"] = SuiviItem::$HOUR_DAYS_WORK;
        }
        $row["time_plus_or_minus"] = isset($user->pointingTemp)  ? $user->pointingTemp->minute_worked  :   "00:00:00";
        $row["days_plus_or_minus"] = $this->convert_time_more_to_day_work($user, $row["time_plus_or_minus"],$row["hours_works"]);
        $row["days_work_nb_dessi"] = (isset( $user->suiviPramsSession->days_work )  && $user->suiviPramsSession->days_work) ? $user->suiviPramsSession->days_work :   ($need_work_info->value + $row["days_plus_or_minus"]);
        $row["days_work_percent_dessi"] = $this->calcul_day_real_working($row["days_work_nb_dessi"], $need_work_info->value);
        $row["sum_note_quality"] =  $row["point_prod"] =  $row["note_prod"] = 0;
        if (isset($user->suiviItems) ) {
            foreach ($user->suiviItems as $suivi_item) {
                $row["point_prod"] += $suivi_item->realPointItem;
                if(isset($suivi_item->noteQuality)){
                    $row["sum_note_quality"] += $suivi_item->noteQuality->sum("note") ;
                }
            }
            $row["sum_note_quality"]    = round($row["sum_note_quality"], 2) ; 
            $row["point_prod"]       = round($row["point_prod"], 2) ; 
        }
        $row["point_per_day"] =   round($row["point_prod"] / $row["days_work_nb_dessi"] ,2) ;
        $row["note_prod"] = round(( $row["point_per_day"]  * 100 ) /  $row["hours_works"] ,2);
        $row["average"] = round(($row["note_prod"] + $row["sum_note_quality"])/2 ,2)  ;

        $row["rating_productivitie"] =  $user->suiviItems->sum("level_sum_point"); //level","point
        $row["rating_qualification"] = "**";
        
        // $row["sum_difficulty"] = $user->suiviItems->sum("level_sum_point");
        foreach ($groupables as $group => $models) {
            foreach ($models as $model) {
                $key =   get_array_value($model, "id") ?? get_array_value($model, "value");
                $title =  get_array_value($model, "title") ?? get_array_value($model, "text");
                $column = "column_{$group}_$key";
                if (!in_array($column, $columns)) {
                    $columns[] = $column;
                    $real_columns[] = ["value" => $column, "text" => $title . ($group == "folders" ? ' (Dossier)' : "")];
                }
                $group_in = "grouped_$group";
                $count = count(get_array_value($user->$group_in, $key, []));
                $class = $count  > 0  ? "text-success fs-5" : "text-danger fs-5";
                // $row[$column] = "<span class='" . $class . "'>" . $count . "</span>";
                $row[$column] =   $count ;
            }
        }
        return $row;
    }
    /** Calcul real day working user in a month without weeekens and dayoff(congés) and public holiday (jour fierié) */
    private function calcul_day_need_working($dayoffs, $nb_days_in_month, $options, $public_holiday = 0)
    {
        $sum_nb_days_dayoff = 0;
        $carbon = new Carbon();
        $year = get_array_value($options, "year", $carbon->now()->year);
        $month = get_array_value($options, "month", $carbon->now()->month);
        foreach ($dayoffs as $dayoff) {
            $start = $carbon->make($dayoff->start_date);
            $end = $carbon->make($dayoff->return_date);
            /**Calcul nb real day in this dayoff without weekends int this month only */
            $nb_day_in_this_one_dayoff = $start->diffInDaysFiltered(function (Carbon $date) use ($year, $month) {
                return (!$date->isWeekend() && $date->month == $month && $date->year == $year);
            }, $end);
            if ($dayoff->start_date_is_morning !=  $dayoff->return_date_is_morning) {
                $dayoff->start_date_is_morning == "0" ? $nb_day_in_this_one_dayoff -= 0.5 : $nb_day_in_this_one_dayoff += 0.5;
            }
            $sum_nb_days_dayoff += $nb_day_in_this_one_dayoff;
        }
        $real_day_need_working = $nb_days_in_month - ($sum_nb_days_dayoff + $public_holiday);
        $data = new stdClass();
        $data->value =  $real_day_need_working;
        $data->html = "<p title = 'Avec total de congés : " . $sum_nb_days_dayoff . " jour(s)'>" . $real_day_need_working . "</p>";
        return $data;
    }
    private function calcul_percent_day_need_working($day_only_working, $days_need_working)
    {
        return  round(($day_only_working * 100) / $days_need_working, 2)  . "%";
    }
    private function calcul_day_real_working($nb_day_real_working, $days_need_working)
    {
        return  round(($nb_day_real_working * 100) / $days_need_working, 2)  . "%";
    }
    private function rgb($base = "green")
    {
        $red = $green  =  $blue = 0;
        $add = 51;
        $$base = 250; // ex : $base = "red" donc $$base vaut $red = 250 ;
        return "rgb($red, $green, $blue)";
    }

    private function convert_time_more_to_day_work($user, $time,$hour_day_work)
    {   $negative = false;
        $hours =  $minutes = 0 ;
        if(str_contains("-" , $time)){
            $negative = true;
        }
        $times = explode(":", $time);
        $hour_day_work = SuiviItem::$HOUR_DAYS_WORK;
        if(count($times) == 2){
            $hours = $times[0] / ($hour_day_work * 60);
        }else{
            $hours = $times[0] / $hour_day_work;
            $minutes = $times[1] / ($hour_day_work * 60);
        }
        $value =  round($hours + $minutes, 2);
        return  $negative ?  (- $value) : $value; 
        
    }
    public function pause_prod(Request $request)
    {
        $message = "En attente";
        $user_pause = SuiviPauseProd::find($request->pause_last_id);
        if ($user_pause && $user_pause->status == "pause") {
            $start  = Carbon::parse(($user_pause->created_at));
            $user_pause->status =  "busy";
            $user_pause->secondes =  now()->diffInSeconds($start);
            $user_pause->save();
            $message = "En cours de traitement";
        } else {
            $auth = Auth::user();
            $user_pause = SuiviPauseProd::create(["user_id" => $auth->id, "status" => "pause"]);
            dispatch(function () use ($auth) {
                \Notification::send(SuiviItem::getMdp(false), new UserPauseProdNotification($auth));
            })->afterResponse();
        }
        return ["success" => true, "message" => $message, "data" => $user_pause];
    }

    /*** Seuil and hours works */
    public function user_on_suivi_list(Request $request)
    {
        $users = User::select("id", "name", "firstname","deleted","registration_number")->withOut(["userJob"])->whereDeleted(0)->get();
        return [ "success" =>  true , "users" => $users];
    }
    /*** Seuil and hours works */
    public function save_hours_days_work(Request $request)
    {
        $auth = Auth::user();
        
        if(!$auth->isM2pOrAdmin() && !$auth->isCp() ){
            return abort(404);
        }
        $data = [];
        if($request->hours_works){
            $data["hours_works"] = str_replace([",",";"],".", $request->hours_works);
        }
        if($request->seuil_point){
            $data["seuil_point"] = str_replace([",",";"],".", $request->seuil_point);
        }
        if($request->days_work){
            $data["days_work"] = str_replace([",",";"],".", $request->days_work);
        }
        SuiviUserParams::updateOrCreate(["user_id" => $request->user_id, "month" => $request->month, "year" => $request->year ] ,  $data);
        return ["success" => true, "message" =>  trans("lang.success_record")];
    }
    public function level_point_dropdown(Request $request)
    {
        $item = SuiviItem::find($request->item_id);
        $selectedDefault = $item ? "selected =true" : "";
        $levels = SuiviVersionLevelPoint::where("version_id", $request->version_id)->whereDeleted(0)->get();
        $html = "<option value='0' $selectedDefault >Diff.</option>";
        foreach ($levels as $level) {
            $selected = ($item && $item->level_id == $level->id) ? "selected=true" : "";
            $html .= "<option value = " . $level->id . "  " . $selected . " >" . $level->level . "</option>";
        }
        return ["success" => true, "data" =>  $html];
    }

    /** set_hidden_show_column_table */
    public function save_hidden_column(Request $request)
    {
        $auth = Auth::user();
        $columns_customed = SuiviColumnCustomed::get_user_hidden_columns();
        $columns_customed->user_id =  $auth->id;
        $hiddened = $columns_customed->columns_hidden ?  explode(",", ($columns_customed->columns_hidden)) : [];
        if (in_array($request->column_rang, $hiddened)) {
            $key = array_search($request->column_rang, $hiddened);
            if ($key !== false) {
                unset($hiddened[$key]);
            }
        } else {
            $hiddened[] = $request->column_rang;
        }
        $columns_customed->columns_hidden = implode(",", $hiddened);
        return  ["success" =>  $columns_customed->save()];
    }

    /** Suivi type client crud */
    public function type_client_modal(Request $request)
    {
        return view("suivis.crud.type-client-modal", []);
    }
    public function save_type_client(Request $request)
    {
        if (!$request->type_client) {
            return ["success" => false,  "message" => "le champ type est obligatoire"];
        }
        $type = SuiviTypeClient::updateOrCreate(["id" => $request->client_type_id], ["name" => $request->type_client, "status" => $request->status]);
        return ["success" => true, "data" => $this->_make_row_type_client($type), "message" => trans("lang.success_record")];
    }
    public function type_client_delete(Request $request, SuiviTypeClient $type)
    {
        if ($request->input("cancel")) {
            dispatch(function () use ($type) {
                $type->update(["deleted" => 0]);
            })->afterResponse();
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row_type_client($type)];
        } else {
            dispatch(function () use ($type) {
                $type->update(["deleted" => 1]);
            })->afterResponse();
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
    public function type_client_data(Request $request)
    {
        $data = [];
        $results = SuiviTypeClient::whereDeleted(0)->whereStatus('on')->latest()->get();
        foreach ($results  as $type) {
            $data[] = $this->_make_row_type_client($type);
        }
        return ["data" =>  $data];
    }
    public function get_type_client(Request $request)
    {
        return ["success" => true, "data" =>  SuiviTypeClient::find($request->id)];
    }

    private function _make_row_type_client(SuiviTypeClient $type)
    {
        return [
            "name" => $type->name,
            "status" => $type->status == "on" ? "<span class='badge badge-light-success'>Actif</span>" : "<span class='badge badge-light-warning'>Non actif</span> ",
            "action" => '<a data-id="' . $type->id . '" href="javascript:void(0)"  class="btn btn-sm btn-clean edit-type-client" title="edit" ><i class="fas fa-edit " style="font-size:12px"></i></a>' . "" .  js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/suivi/delete/type-client/$type->id"), "class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"]),
        ];
    }

    /** Cron --  Mettre en pause les dossiers en cours à {hour} heure */
    /** This route is called by cron form  schedule:suivi command */
    public function make_pause_all_suivi_item()
    {
        // SuiviItem::whereDeleted(0)->where("id",79)->where("status_id" , SuiviItem::$IN_PROGRESS)->update(["status_id" => SuiviItem::$PAUSE]);
        SuiviItem::whereDeleted(0)->where("id",1)->update(["status_id" => SuiviItem::$PAUSE]);
        return ["success" => true, "message" => "Toutes les dossiers sur la table suivi sont mise en pause date : " .  now()->format("d-M-Y h:m:s")];
    }
}
