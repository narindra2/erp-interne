<?php

namespace App\Http\Controllers;

use App\Models\EmployeeCheck;
use App\Models\MvtAttendance;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MvtAttendanceController extends Controller
{
    //
    public function index($idUser)
    {
        $data = [];
        $data['title'] = "31 - Gestion des présences";
        $data['users_id'] = $idUser;

        return view('mvt-attendances.index', $data);
    }

    public function displayDataInFullCalendar($idUser, Request $request)
    {
        if ($request->ajax()) {
            $month = isset($request->month) ? $request->month : Carbon::now()->month;
            $year = isset($request->year) ? $request->year : Carbon::now()->year;

            $mvts = MvtAttendance::getEmployeeMvtByType($idUser, null, $year, $month);

            $events = [];

            foreach($mvts as $mvt) {
                $event['title'] = '';
                $event['start'] = $mvt->day;
                $event['end'] = $mvt->day;
                $event['type'] = $mvt->type;
                $events[] = $event;
            }
            return response()->json($events);
        }
    }

    public function goToImportExcelPage()
    {
        $data = array();
        $data['title'] = '31 - Import pointage';
        return view('mvt-attendances.form-with-import-excel', $data);
    }

    public function readExcelFile(Request $request)
    {
        $data = $request->validate([
            'file' => ['required']
        ]);
        try {
            $file = $request->file('file');
            EmployeeCheck::importDataFormExcelFile($file);
            return back()->withInput(['success' => 'Import fait avec succès']);
        }
        catch(Exception $e) {
            return back()->withInput(['error' => $e->getMessage()]);
        }
    }

    public function storeAttendances($attendances) 
    {
        DB::beginTransaction();
        DB::commit();
        DB::rollback();
    }
}
