<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DayOff;
use App\Models\Job;
use App\Models\User;
use App\Models\UserJob;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PayslipController extends Controller
{
    //
    private $gate_payslip = 'payslip';

    public function index($idUser)
    {
        //Gate::authorize($this->gate_payslip);
        $data = [];
        $data['title'] = 'Bulletin de paie';
        $data['user'] = User::find($idUser);
        $data['jobs'] = Job::whereDeleted(0)->get();
        $data['userJobs'] = UserJob::with(['job', 'user'])->whereDeleted(0)->where('users_id', $idUser)->orderByDesc('date_user_job')->get();
        if (count($data['userJobs']) == 0)  $data['info'] = new UserJob();
        else                                $data['info'] = $data['userJobs'][0];
        return view('employee-payslip.index', $data);
    }

    public function displayDataInFullCalendar(Request $request)
    {
        if ($request->ajax()) {
            $data = array();

            $attendances = Attendance::whereDeleted(0)
                ->where('users_id', $request->users_id)
                ->whereBetween('attendance_date', [$request->start, $request->end])->get();
            
            $daysOff = DayOff::whereDeleted(0)
                ->where('users_id', $request->users_id)
                ->where('result', 1)
                ->whereBetween('start_date', [$request->start, $request->end])->get();

            foreach($attendances as $attendance) {
                $attendance['title'] = '';
                $attendance['start'] = $attendance['attendance_date'];
                $attendance['end'] = $attendance['attendance_date'];
                $attendance['type'] = 0;
                $data[] = $attendance;
            }

            foreach($daysOff as $dayOff) {
                $dayOff['title'] = '';
                $dayOff['start'] = $dayOff['start_date'];
                $dayOff['end'] = Carbon::make($dayOff['start_date'])->addDays($dayOff->duration);
                $dayOff['type'] = 1;
                $dayOff['duration'] = $dayOff->duration;
                $data[] = $dayOff;
            }
            return response()->json($data);
        }
    }

    public function payment($idUser)
    {
        $data = [];
        $data['title'] = 'Bulletin de paie';
        
        $date = new DateTime();
        $data['period'] = '1 ' . $date->format('M') . ' au ' . date('t') . " " . $date->format('M');
        $data['userJobs'] = UserJob::with(['job', 'user'])->whereDeleted(0)->where('users_id', $idUser)->orderByDesc('date_user_job')->get();
        if (count($data['userJobs']) == 0)  $data['info'] = new UserJob();
        else                                $data['info'] = $data['userJobs'][0];
        $data['nbDaysPerformed'] = count(Attendance::whereDeleted(0)->where('users_id', $idUser)->whereRaw('MONTH(attendance_date) = ?', 10)->get());
        $data['nbDaysOff'] = DayOff::countNbDaysOff(10, $idUser);
        $data['salaryPerDay'] = $data['info']->salary / date('t');
        $data['totalAmountDaysPerformed'] = $data['salaryPerDay'] * $data['nbDaysPerformed'];
        $data['totalAmountDaysOff'] = $data['salaryPerDay'] * $data['nbDaysOff'];
        $data['salaryNet'] = $data['totalAmountDaysPerformed'] + $data['totalAmountDaysOff'];
        return view('employee-payslip.payment talo', $data);
    }
}
