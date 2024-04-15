<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\EmployeeCheck;
use App\Models\EmployeeDailyActivity;
use App\Models\PublicHoliday;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EmployeeActivityController extends Controller
{
    public function goToImportExcelPage()
    {
        Gate::authorize("attendance_import");
        $data = array();
        $data['title'] = '31 - Import pointage';
        return view('mvt-attendances.form-with-import-excel', $data);
    }

    public function readExcelFile(Request $request)
    {
        Gate::authorize("attendance_import");
        $data = $request->validate([
            'date' => ['required', 'date'],
            'file' => ['required']
        ]);
        $file = $request->file('file');
        try {
            EmployeeCheck::importDataFormExcelFile($file);
            DB::beginTransaction();
            $minutes = EmployeeCheck::calculHoursPerformed($request->date);
            EmployeeDailyActivity::saveAttendances($minutes, $request->date);
            EmployeeCheck::query()->delete();
            DB::commit();
            return back()->withInput(['success' => 'Import fait avec succès']);
        }
        catch(Exception $e) {
            DB::rollBack();
            return back()->withInput(['error' => $e->getMessage()]);
        }
    }

    public function viewCalendar(Request $request)
    {
        Gate::authorize("calendar-attendance");
        $month = isset($request->month) ? $request->month : Carbon::now()->month;
        $year = isset($request->year) ? $request->year : Carbon::now()->year;
        $page = isset($request->page) ? $request->page : 0;
        $nbUserToDisplay = 10;

        //Date Parameter 
        if ($month <= 0)  {
            $month = 12;
            $year -= 1;
        }

        if ($month > 12) {
            $month = 1;
            $year += 1;
        }

        $data = [];
        $data['title'] = "31 - Calendrier des activités des employés";
        $data['month'] = $month;
        $data['year'] = $year;
        $data['actualDate'] = Carbon::make("$year-$month-1")->monthName . " " . $year;
        $nbUsers = User::whereDeleted(0)->count();
        $data['pagination'] = ceil($nbUsers / $nbUserToDisplay);
        $data['page'] = $page;
        $data['users'] = User::getUsersWithDailyActivity($year, $month, $page, $nbUserToDisplay);
        $data['dailyActivities'] = DailyActivity::whereDeleted(0)->get();
        $data['colspan'] = $data['dailyActivities']->count();
        $data['publicHolidays'] = PublicHoliday::getPublicHolidays($year, $month);
        $data['days'] = [];
        $nbDaysInMonth = Carbon::make("$year-$month-1")->daysInMonth;
        for ($i=1; $i<=$nbDaysInMonth; $i++) {
            $data['days'][] = Carbon::make("$year-$month-$i")->locale('fr_FR')->dayName . " " . $i;
            $data['dates'][] = Carbon::make("$year-$month-$i")->format("Y-m-d H:i:s");
        }
        return view("employee-activity.calendar", $data);
    }

    public function store(Request $request)
    {
        Gate::authorize("calendar-attendance");
        $request->validate([
            'users_id' => ['required'],
            'daily_activity_id' => ['required'],
            'time' => ['required'],
            'day' => ['required']
        ]);
        EmployeeDailyActivity::saveEmployeeActivity($request->input());
        return back();
    }

}
