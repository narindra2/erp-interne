<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Menu;
use App\Models\User;
use App\Models\HourRecovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\HourRecoveryRequest;

class HourRecoveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('hour-recoveries.index',["basic_filter" => HourRecovery::createFilter()]);
    }

    /**
     * Get the data
     */
    public function data_list(Request $request)
    {
        $auth = Auth::user();
        $is_can_access_to_valid = $auth->isAdmin() || $auth->isHR() || $auth->isCp() || in_array( $auth->id, Menu::$USER_ALLOWED_PART_ACCESS["complement_hours"]);
        $hourRecoveries = HourRecovery::getDetail($request->all())->get();
        $data = [];
        foreach ($hourRecoveries as $hourRecovery) {
            $data[] = $this->_make_row($hourRecovery , $is_can_access_to_valid);
        }
        return ["data" => $data];
    }

    public function _make_row(HourRecovery $hourRecovery , $is_can_access_to_valid = false)
    {
        $row = [];
        $row["DT_RowId"] = row_id("hourRecovery", $hourRecovery->id);
        $row['date_of_absence'] = $hourRecovery->date_of_absence->format('d M Y');
        $row['fullname'] = $hourRecovery->user->sortname;
        $row['job'] = $hourRecovery->job->name;
        $row['recovery_date'] = $hourRecovery->recovery_date;
        $row['duration_of_absence'] = $hourRecovery->getDuration();
        $row['hour_absence'] = $hourRecovery->hour_absence;
        $row['description'] = $hourRecovery->description;
        $row['is_validated'] = $hourRecovery->isValidated();
        $row['response'] = "";
        $row['action'] = "";
        $row['delete']  = "";
        if ($hourRecovery->is_validated !== null) {
            $row['action'] = "";
        }
        if ($is_can_access_to_valid) {
            $row['response'] .= modal_anchor(url("/modal-hour-recoveries-response/$hourRecovery->id"), "<button class='btn btn-success btn-sm'> <i class='fas fa-reply'></i></button>", ["title" => "Donner un résultat à la demande"]);
        }
        /** Can update it */
        if ($is_can_access_to_valid || (Auth::id() == $hourRecovery->user_id && !$hourRecovery->is_validated)) {
            $row['delete'] = js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/hour-recoveries/delete"),"data-post-hour_recovery_id" =>$hourRecovery->id ,"class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"]);
            $row['action'] = modal_anchor(url("/hour-recoveries/form/$hourRecovery->id"), '<i class="fas fa-pen"></i>', ['title' => "Modifier", 'class' => '' , "data-modal-lg" => true,  "data-post-hour_recovery_id" =>$hourRecovery->id]);;
        }
        return $row;
    }

    public function show_modal_form(HourRecovery $hourRecovery)
    {
        if ($hourRecovery){
            $hourRecovery->createForm(Auth::user());
        }
        return view('hour-recoveries.modal.form-hour-recovery-modal', compact('hourRecovery'));
    }

    public function modal_response(HourRecovery $hourRecovery)
    {
        return view('hour-recoveries.modal.response-request-modal', compact('hourRecovery'));
    }

    public function response_request(Request $request) 
    {
        if ($request->response === null) {
            return ["success" => false, "message" => 'Veuillez donner un résultat'];
        }
        $hourRecovery = HourRecovery::find($request->id);
        $hourRecovery->is_validated = $request->response;
        $hourRecovery->save();
        return ['success' => true, "message" => 'La réponse a été enregistrée', 'row_id' => row_id("hourRecovery", $hourRecovery->id), 'data' => $this->_make_row($hourRecovery)];
    }   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HourRecoveryRequest $request)
    {
      
        try {
            $hourRecovery = HourRecovery::createHourRecovery($request->all());
            $auth = Auth::user();
            $is_can_access_to_valid = $auth->isAdmin() || $auth->isHR() || $auth->isCp() || in_array( $auth->id, Menu::$USER_ALLOWED_PART_ACCESS["complement_hours"]);
            return ['success' => true, 'message' => "La demande de récupération d'heure a été créée avec succès", "data" => $this->_make_row($hourRecovery ,  $is_can_access_to_valid)];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HourRecovery  $hourRecovery
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $hourRecovery = HourRecovery::find($request->hour_recovery_id);
        if ($request->input("cancel")) {
            $hourRecovery->update(["deleted" => 0]);
            $auth = Auth::user();
            $is_can_access_to_valid = $auth->isAdmin() || $auth->isHR() || $auth->isCp() || in_array( $auth->id, Menu::$USER_ALLOWED_PART_ACCESS["complement_hours"]);
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row($hourRecovery , $is_can_access_to_valid)];
        } else {
            $hourRecovery->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HourRecovery  $hourRecovery
     * @return \Illuminate\Http\Response
     */
    public function show(HourRecovery $hourRecovery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HourRecovery  $hourRecovery
     * @return \Illuminate\Http\Response
     */
    public function edit(HourRecovery $hourRecovery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HourRecovery  $hourRecovery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HourRecovery $hourRecovery)
    {
        //
    }

   
}
