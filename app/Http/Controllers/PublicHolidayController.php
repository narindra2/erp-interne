<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicHolidayRequest;
use App\Http\Resources\PublicHolidayResource;
use App\Models\PublicHoliday;
use Illuminate\Http\Request;

class PublicHolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('public-holidays.index');
    }

    /**
     * Return the resource's data
     * @return array
     */
    public function data_list(Request $request)
    {
        $publicHolidays = PublicHoliday::getPublicHolidays($request->year, $request->month);
        return PublicHolidayResource::collection($publicHolidays);
    }

    /**
     * Show the modal for creating or updating the table publicHoliday
     * @return \Illuminate\Http\Response
     */
    public function modal_form(PublicHoliday $publicHoliday)
    {
        return view("public-holidays.modal.form-modal", ["publicHoliday" => $publicHoliday]);
    }

    /**
     * Save or Update the resource
     * @return \Illuminate\Http\Response
     */
    public function updateOrCreate(PublicHolidayRequest $request)
    {
        $publicHoliday = PublicHoliday::updateOrCreate(
            ['id' => $request->id], 
            $request->except("_token")
        );
        return ["success" => true ,"message" => trans("lang.success_record") ,"row_id" =>  $request->id ? row_id("publicHoliday", $publicHoliday->id) : null, "data" => new PublicHolidayResource($publicHoliday) ];
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PublicHoliday  $publicHoliday
     * @return \Illuminate\Http\Response
     */
    public function show(PublicHoliday $publicHoliday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PublicHoliday  $publicHoliday
     * @return \Illuminate\Http\Response
     */
    public function edit(PublicHoliday $publicHoliday)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PublicHoliday  $publicHoliday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PublicHoliday $publicHoliday)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PublicHoliday  $publicHoliday
     * @return \Illuminate\Http\Response
     */
    public function destroy(PublicHoliday $publicHoliday)
    {
        //
        $publicHoliday->deleted = 1;
        $publicHoliday->save();
        return ["success" => true ,"message" => trans("lang.success_record") ,"row_id" =>  $publicHoliday->id ? row_id("publicHoliday", $publicHoliday->id) : null, "data" => new PublicHolidayResource($publicHoliday) ];
    }
}
