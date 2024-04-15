<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{

    public function index()
    {
        $data = array();
        $data['title'] = "31 - Les emplois";
        return view("jobs.index", $data);
    }

    public function data_list()
    {
        $jobs = Job::whereDeleted(0)->get();
        $data = [];
        foreach ($jobs as $job) {
            $data[] = $this->make_row($job);
        }
        return ['data' => $data];
    }

    public function make_row(Job $job)
    {
        $row = [];
        $row["DT_RowId"] = row_id("job", $job->id);
        $row['name'] = $job->name;
        $row['actions'] = view('jobs.column.actions', array("job" => $job))->render();
        return $row;
    }

    public function modal_form(Job $job)
    {
        return view("jobs.modal.form-modal", ["job" => $job]);
    }

    public function saveOrUpdateJob(Request $request)
    {
        $job = Job::updateOrCreate(
            ['id' => $request->id], 
            $request->except("_token")
        );
        return ["success" => true ,"message" => trans("lang.success_record") ,"row_id" =>  ($request->id ? row_id("job", $job->id) : null), "data" => $this->make_row($job) ];
    }

    public function deleteJob(Job $job) 
    {
        $job->deleted = 1;
        $job->save();
        return ["success" => true ,"message" => trans("lang.success_record") ,"row_id" =>  $job->id ? row_id("job", $job->id) : null, "data" => [] ];
    }
}
