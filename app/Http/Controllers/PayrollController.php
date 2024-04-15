<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\UserJob;
use App\Models\UserJobView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
    //
    private $gate_payroll = 'payroll'; 

    public function index()
    {
        Gate::authorize($this->gate_payroll);
        $data = array();
        $data['title'] = "Gestion de paie";
        $data['jobs'] = Job::whereDeleted(0)->get();
        return view('payroll.list', $data);
    }

    public function addOrUpdateHistoric(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => ['required'],
            'jobs_id' => ['required'],
            'salary' => ['required', 'numeric', 'min:1'],
            'date_user_job' => ['required', 'date']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'errors' => $validator->errors()->all()
            ]);
        }

        UserJob::updateOrCreate([
                'id' => $request->id
            ], [
                'users_id' => $request->users_id,
                'jobs_id' => $request->jobs_id,
                'salary' => $request->salary,
                'date_user_job' => $request->date_user_job
            ]);
            
        return response()->json([
            'status' => 200,
            'success' => 'OK'
        ]);
    }

    public function deleteHistoric(Request $request)
    {
        $id = $request->id;
        $userJob = UserJob::findOrFail($id);
        $userJob->deleted = true;
        $userJob->save();
        return back();
    }

    public function getActualUserJob() 
    {
        $users = UserJobView::with(['user.type', 'job', 'contractType'])->get()->filter(function($user) {
            return $user->user->deleted == 0;
        });
        return datatables($users)
            ->addColumn('registration_number', function($user) {
                $id = $user->user->id;
                return $user->user->registration_number;
            })
            ->addColumn('name', function($user) {
                return $user->user->name;
            })
            ->addColumn('firstname', function($user) {
                return $user->user->firstname;
            })
            ->addColumn('job', function($user) {
                return $user->job->name;
            })
            ->addColumn('contract_type', function($user) {
                return $user->contractType->acronym;
            })
            ->addColumn('user_type', function($user) {
                return $user->user->type->name;
            })
            ->addColumn('actions', function($user) {
                return view('payroll.subview.button-actions', array('userJob' => $user));
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    // public function userPayslip($id)
    // {
    //     $data = [];
    //     $data['title'] = 'Bulletin de paie';
    //     $data['jobs'] = Job::whereDeleted(0)->get();
    //     $data['users_id'] = $id;
    //     $data['userJobs'] = UserJob::with(['job', 'user'])->whereDeleted(0)->where('users_id', $id)->orderByDesc('date_user_job')->get();
    //     $data['info'] = $data['userJobs'][0];
    //     $data['attendances'] = Attendance::whereDeleted(0)->where('users_id', $id)->get();
    //     return view('payroll.attendance', $data);
    // }
}
