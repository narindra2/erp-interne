<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    //
    public function getAttendances($userId)
    {
        $attendances = Attendance::whereDeleted(0);
        if ($userId != null)  $attendances->where('users_id', $userId);
        return $attendances->get();
    }

    public function getNbDaysPerformed(Request $request)
    {
        if ($request->ajax()) {       
            $count = Attendance::selectRaw('COUNT(*) as nb, users_id, MONTH(attendance_date)')
            ->whereDeleted(0)
            ->where('users_id', $request->users_id)
            ->whereRaw('MONTH(attendance_date) = ? AND YEAR(attendance_date) = ?', [$request->month, $request->year])
            ->first()->nb;
            return response()->json(['nb' => $count]);
        }
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $attendance = Attendance::whereDeleted(0)->where('users_id', $request->users_id)
            ->where('attendance_date', $request->attendance_date)->first();
            $exist = "0";
            $id = "";
            if ($attendance == null) {
                $exist = "1";
                $att = Attendance::create([
                    'users_id' => $request->users_id,
                    'attendance_date' => $request->attendance_date
                ]);
                $id = $att->id;    
            } 
            return response()->json(['success' => 'OK', 'att_exist' => $exist, 'id' => $id]);
        }
    }

    public function destroy(Request $request)
    {
        $attendance = Attendance::find($request->id);
        $attendance->deleted = 1;
        $attendance->save();
        return response()->json(['success' => 'OK']);
    }
}
