<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    use HasFactory;
    public $table = "contract_type";
    public static $_PE_CONTRAT = 1;
    public static $_PE_CONTRAT_RENEW = 5;
    public static $_PE_END_DAY_AFTER_HIRING_DATE = 90; // finish pe afert 3 month

    public static function getEndDayPE($contractID) {
        $end = self::$_PE_END_DAY_AFTER_HIRING_DATE;
        if ($contractID != self::$_PE_CONTRAT) {
            return $end * 2;
        }
        return $end;
    }
}
