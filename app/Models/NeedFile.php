<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDF;

class NeedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'src',
        'name',
        'amount'
    ];

    public static function createInvoice($input) {
        $details = DetailNeed::getStatistic($input);
        $sum = 0;
        $date = get_array_value($input, "date");
        $date = getDateInDateRange($date);

        foreach($details as $detail) {
            $sum += $detail->total_price;
        }

        $registrationNumber = NeedFile::getNewRegistrationNumber();
        $file = "Facture-$registrationNumber.pdf";
        $pdf = PDF::loadView('need-to-buy.pdf.index', ['details' => $details, 'sum' => $sum, 'date' => $date, 'registration_number' => $registrationNumber]);
        $path = public_path("invoices/") . $file;
        $pdf->save($path);

        NeedFile::create([
            'name' => $file,
            'src' => $path,
            'amount' => $sum,
            'registration_number' => $registrationNumber
        ]);
        return $pdf->download($file);
    }

    public static function getNewRegistrationNumber() {
        $registrationNumber = NeedFile::selectRaw("MAX(id) + 1 as id")->first()->id . "";
        while (strlen($registrationNumber) < 6) {
            $registrationNumber = "0" . $registrationNumber;
        }
        return $registrationNumber;
    }
}