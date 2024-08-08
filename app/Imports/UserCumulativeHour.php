<?php

namespace App\Imports;

use App\Models\User;
use App\Models\PointingTemp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserCumulativeHour implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            try {
                $user = User::where("registration_number", $row[0])->first();
                if ($user && $row[1]) {
                    PointingTemp::saveOrUpdatePointingTemp(["user_id" => $user->id ,"minute_worked" => $row[1]]);
                }
            } catch (\Throwable $th) {
               
            }
        }
       
    }
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'ISO-8859-1'
        ];
    }
    public function rules(): array
    {
        return [
            '1' => ['string'],
            '2' => ['string'],
        ];
    }
   
}
