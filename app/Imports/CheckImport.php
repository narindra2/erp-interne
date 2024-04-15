<?php

namespace App\Imports;

use App\Models\CheckVerificationTemp;
use Exception;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CheckImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row['dateTime'] = $row['date'] + $row['time'];
        if ($row['no']) {
            return new CheckVerificationTemp([
                //
                'registration_number' => $row['no'],
                'on' => 'device',
                'date_time' => Date::excelToDateTimeObject($row['dateTime'])->format('Y-m-d H:i:s')
            ]);
        }
    }
}
