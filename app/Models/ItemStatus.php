<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStatus extends Model
{
    use HasFactory;

    public static $_OK = 1;

    public static function convertToSelect() {
        $itemStatuses = ItemStatus::whereDeleted(0)->get();
        $options = [];
        foreach ($itemStatuses as $itemStatus) {
            $option = [];
            $option['text'] = $itemStatus->name;
            $option['value'] = $itemStatus->id;
            $options[] = $option;
        }
        return $options;
    }
}
