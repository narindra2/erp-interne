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
            $string  =  $row->first();
            $registration_number = $time = "";
            if (str_contains($string, ';')) {
               $exploded = explode(";",$string);
               $registration_number = $exploded[0];
               $time = $exploded[1];
            }elseif (str_contains($string, ',')) {
                $exploded = explode(",",$string);
                $registration_number = $exploded[0];
                $time = $exploded[1];
            }else{
                $registration_number = $row[0];
                $time = $row[1];
            }
            try {
                $user = User::where("registration_number", $registration_number)->first();
                if ($user && $time) {
                    PointingTemp::saveOrUpdatePointingTemp(["user_id" => $user->id ,"minute_worked" => $time]);
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
