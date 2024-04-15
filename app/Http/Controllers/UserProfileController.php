<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\UserJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    //
    public function index()
    {
        $data = array();
        $data['title'] = 'Acceuil';
        $data['user'] = Auth::user();
        $userJobs = UserJob::with(['job'])->whereDeleted(0)->where('users_id', $data['user']->id)->orderByDesc('date_user_job')->get(); 
        $data['userJobs'] = $userJobs;
        try {
            $data['actualJob'] = $data['userJobs'][0]->job;
        }
        catch(Exception $e) {
            $data['actualJob'] = new Job();
        }
        return view('userProfile.profile', $data);
    }

    public function editUserInformation(Request $request) 
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => ['required'],
                'name' => ['required'],
                'firstname' => ['required'],
                'birthdate' => ['required', 'date'],
                'address' => ['required'],
                'CIN' => ['required']
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()->all()
                ]);
            }
            
            $user = User::find($request->id);
            $user->name = $request->name;
            $user->firstname = $request->firstname;
            $user->birthdate = $request->birthdate;
            $user->address = $request->address;
            $user->CIN = $request->CIN;
            $user->save();
            return response()->json([
                'success' => 'OK'
            ]);
        }
    }
    
    
}
