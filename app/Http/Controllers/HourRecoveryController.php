<?php

namespace App\Http\Controllers;

use App\Http\Requests\HourRecoveryRequest;
use App\Models\HourRecovery;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $hourRecoveries = HourRecovery::getDetail($request->all())->get();
        $data = [];
        foreach ($hourRecoveries as $hourRecovery) {
            $data[] = $this->make_row($hourRecovery);
        }
        return ["data" => $data];
    }

    public function make_row(HourRecovery $hourRecovery)
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
        if ($hourRecovery->is_validated !== null)   $row['action'] = "";
        else {
            $row['action'] = modal_anchor(url("/modal-hour-recoveries-response/$hourRecovery->id"), "<button class='btn btn-success btn-sm'>Répondre</button>", ["title" => "Donner un résultat à la demande"]);
        }
        return $row;
    }

    public function show_modal_form(HourRecovery $hourRecovery)
    {
        if ($hourRecovery != null)  $hourRecovery->createForm(Auth::user());
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
        return ['success' => true, "message" => 'La réponse a été enregistrée', 'row_id' => row_id("hourRecovery", $hourRecovery->id), 'data' => $this->make_row($hourRecovery)];
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
            $hourRecovery = HourRecovery::createHourRecovery($request->input());
            return ['success' => true, 'message' => "La demande de récupération d'heure a été créée avec succès", "data" => $this->make_row($hourRecovery)];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HourRecovery  $hourRecovery
     * @return \Illuminate\Http\Response
     */
    public function destroy(HourRecovery $hourRecovery)
    {
        //
    }
}
