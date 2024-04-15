<?php

namespace App\Http\Controllers;

use App\Http\Resources\PointingResumeResource;
use App\Models\Check;
use App\Models\CheckVerificationTemp;
use App\Models\PointingResume;
use App\Models\User;
use App\Notifications\NegativeCumulativeHourNotification;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckInController extends Controller
{

    public function pagePointingExcel()
    {
        # code...
        $data = [];
        $data['basic_filter'] = Check::createFilter();
        return view('checkin.check-excel', $data);
    }

    public function data_list_excel(Request $request)
    {
        $checks = Check::getDetails(Check::$_DEVICE, $request->date_time);
        $data = [];
        foreach ($checks as $check) {
            $data[] = $this->make_row_excel($check);
        }
        return ['data' => $data];
    }

    public function make_row_excel(Check $check)
    {
        $row = [];
        $row['date'] = $check->date;
        $row['registration_number'] = $check->registration_number;
        $row['entry_time'] = $check->getEntryTimeWithFormat();
        $row['exit_time'] =  $check->getExitTimeWithFormat();
        $row['time_works'] = $check->getTimeWorks();
        $row['cumulative_hour'] = $check->getCumulativeHourString();
        return $row;
    }

    public function formModalImportExcel()
    {
        return view('checkin.import-excel-modal');
    }


    /*
        After entering their information to access the platform,
        the system will redirect the user to the CheckInController controller
    */
    public function index()
    {
        # code...
        $data = [];
        $data['basic_filter'] = Check::createFilter();
        return view('checkin.index', $data);
    }

    public function data_list(Request $request)
    {
        $checks = Check::getDetails(Check::$_WEB, $request->date_time);
        $data = [];
        foreach ($checks as $check) {
            $data[] = $this->make_row($check);
        }
        return ['data' => $data];
    }

    public function make_row(Check $check)
    {
        $row = [];
        $row['date'] = $check->date;
        $row['registration_number'] = $check->registration_number;
        $row['entry_time'] = $check->getEntryTimeWithFormat();
        $row['exit_time'] =  $check->getExitTimeWithFormat();
        $row['time_works'] = $check->getTimeWorks();
        $row['cumulative_hour'] = $check->getCumulativeHourString();
        return $row;
    }

    public function importFingerpointPointingInExcel(Request $request)
    {
        $file = $request->file('file');
        DB::beginTransaction();
        try {
            CheckVerificationTemp::importExcel($file);
            CheckVerificationTemp::verifyDataIsOdd();
            CheckVerificationTemp::saveToTableCheck();
            CheckVerificationTemp::saveToPointingResume();
            DB::commit();
            return ["success" => true,"message" => "Import fait avec succès"];
        }
        catch(Exception $e) {
            DB::rollBack();
            return ["success" => false,"message" => $e->getMessage()];
        }
    }
    public function negative_hour()
    {
        $data = [];
        $data['basic_filter'] = Check::createFilter();
        return view('checkin.negative-hour', $data);
    }

    public function data_list_negative(Request $request)
    {
        $checks = Check::getDetails(Check::$_WEB, $request->date_time);
        $data = [];
        foreach ($checks as $check) {
            if(!empty($this->make_row_negative($check))) $data[] = $this->make_row_negative($check);
        }
        return ['data' => $data];
    }

    public function make_row_negative(Check $check)
    {
        $row = [];
        if($check->getCumulativeHourStringNegative() != null){
            $row['date'] = $check->date;
            $row['registration_number'] = $check->registration_number;
            $row['name'] = $check->user;
            $row['cumulative_hour_negative'] = $check->getCumulativeHourStringNegative();
        }
        return $row;
    }

    public function cumul_negative_notification(Request $request)
    { 
        $checks = Check::getDetails(Check::$_WEB, $request->date);
        foreach ($checks as $check){
            if($check->getCumulativeHourStringNegative() != null){
                $user = User::where('registration_number', $check->registration_number)->first();  
                \Notification::send($user, (new NegativeCumulativeHourNotification($check->getCumulativeHourStringNegative())));
            }
        }
        return ["success" => true,"message" => "Notification envoyé"];
    }

    public function resumePage()
    {
        $data = [];
        $data['basic_filter'] = PointingResume::createFilter();
        return view('checkin.resume', $data);
    }

    public function data_list_pointing_resume(Request $request)
    {
        $date = $request->date;
        $startDate = Carbon::today()->firstOfMonth();
        $endDate = null;
        if ($date) {
            $startDate = Carbon::make(to_date(explode("-", $date)[0]));
            $endDate = Carbon::make(to_date(explode("-", $date)[1]));
            if (!$startDate->eq($endDate))  $endDate->addDay();
        }
        $resume = PointingResume::getCumulativeHours(null, $startDate, $endDate);
        return PointingResumeResource::collection($resume);
    }

    public function modal_detail_resume(Request $request) {
        $details = PointingResume::getDetails($request->registration_number, null, null);
        return view('checkin.detail-modal', ["details" => $details]);
    }

    public function data_detail(Request $request) {
        $details = PointingResume::getDetails($request->registration_number, null, null);
    }

    public function userChrono() 
    {
        $minute_worked = Check::chrono(auth()->user());
        return ['success' => 'OK', 'data' => $minute_worked];
    }
}
