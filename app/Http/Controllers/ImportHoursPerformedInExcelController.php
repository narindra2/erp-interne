<?php

namespace App\Http\Controllers;

use App\Events\ImportHoursPerformedInExcel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ImportHoursPerformedInExcelController extends Controller
{
    //
    public function index()
    {
        Gate::authorize("attendance_import");
        $data = array();
        $data['title'] = '31 - Import pointage';
        return view('mvt-attendances.form-with-import-excel', $data);
    }

    public function store(Request $request)
    {
        Gate::authorize("attendance_import");
        
        //Required input
        $data = $request->validate([
            'date' => ['required', 'date'],
            'file' => ['required']
        ]);

        try {
            DB::beginTransaction();
            event(new ImportHoursPerformedInExcel($request->date, $request->file('file')));
            DB::commit();
            //return back()->with("success", "L'import a été un succès");
        }
        catch(Exception $e) {
            DB::rollBack();
            return back()->withInput(['error' => $e->getMessage()]);
        }
    }
}
