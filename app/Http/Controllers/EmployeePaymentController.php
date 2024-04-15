<?php

namespace App\Http\Controllers;

use App\Models\EmployeePayment;
use App\Models\SocialCharge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;

class EmployeePaymentController extends Controller
{

    public function __construct()
    {
        set_time_limit(8000000);
    }

    public function getPeriod($year, $month)
    {
        $period = "";
        return $period;
    }

    public function index($idUser, Request $request)
    {
        $data = [];
        $data['title'] = '31 - Fiche de paie';

        $month = isset($request->month) ? $request->month : Carbon::now()->month;
        $year = isset($request->year) ? $request->year : Carbon::now()->year;

        $data['payslip'] = EmployeePayment::getForm($idUser, $year, $month);
        $data['socialCharges'] = SocialCharge::whereDeleted(0)->get();
        $data['month'] = $month;
        $data['year'] = $year;
        return view('employee-payslip.payment', $data);
    }

    public function exportPDF(Request $request)
    {
        $payslip = EmployeePayment::getFormById($request->id);
        $urlPhoto = public_path() . "/assets/images/logo_PDF.png";
        // $urlPhoto = url("/assets/images/logo_PDF.png");
        // return view('employee-payslip.payslip-pdf', compact('payslip', 'urlPhoto'));
        $pdf = PDF::loadView('employee-payslip.payslip-pdf', compact('payslip', 'urlPhoto'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download("fiche.pdf");
    }

    public function store(Request $request)
    {
        try {
            $socialCharges = SocialCharge::whereDeleted(0)->get();
            EmployeePayment::saveForm($request->input(), $socialCharges);
            return back();
        }
        catch(Exception $e) {
            return back()->withInput(['error' => $e->getMessage()]);
        }
    }
}