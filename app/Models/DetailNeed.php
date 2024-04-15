<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class DetailNeed extends Model
{
    use HasFactory;

    public $fillable = [
        'need_to_buy_id',
        'qty',
        'status_date',
        'author_id',
        'status',
        'item_type_id',
        'department_id',
        'unit_item_id',
    ];

    protected $casts = [
        'status_date' => 'datetime'
    ];
    public static $_VALIDATED = "validé";
    public static $_REFUSED = "refusé";
    public static $_STATUSES = ["validé", "refusé", "acheté"];
    public static $_STATUSES_CLASS = ["primary", "danger", "success"];

    public function author() {
        return $this->belongsTo(User::class, "author_id");
    }

    public function itemType() {
        return $this->belongsTo(ItemType::class, "item_type_id");
    }

    public function unit() {
        return $this->belongsTo(UnitItem::class, 'unit_item_id');
    }

    public function needToBuy() {
        return $this->belongsTo(NeedToBuy::class, "need_to_buy_id");
    }

    public function getStatusHtmlAttribute() {
        $class = "success";
        $status = $this->status;
        if ($status == DetailNeed::$_VALIDATED) $class = "primary";
        else if ($status == DetailNeed::$_REFUSED) $class = "danger";
        return "<span class='badge badge-light-$class'>$status</span>";
    }

    public static function getStatistic($input) {
        $status = get_array_value($input, "status");
        $date = get_array_value($input, "date");
        $department_id = get_array_value($input, "department_id");
        $data = DetailNeed::with(['itemType'])->selectRaw("SUM(qty) as qty, item_type_id");
        if ($status)    $data->where("status", $status);
        if ($date) {
            $date = getDateInDateRange($date);
            $data->whereBetween('status_date', $date);
        }
        if ($department_id)    $data->where("department_id", $department_id);
        $data->groupBy("item_type_id");
        $data = $data->get();
        return $data;
    }

    public function rowStatistic() {
        return [
            "DT_RowId" => row_id("need_stat", $this->id),
            "name" => $this->itemType->name,
            "unit_price" => $this->itemType->unit_price,
            "quantity" => $this->qty,
            "total_price" => $this->total_price
        ];
    }

    public function getTotalPriceAttribute() {
        return $this->itemType->unit_price * $this->qty;
    }

    public static function createFilterStat() {
        $filters = [];
        $filters[] = [
            'label' => 'Date',
            'attributes' => [
                'placeholder' => 'Date'
            ],
            'name' => 'date',
            'type' => 'date-range'
        ];
        $filters[] = [
            'label' => 'Departement',
            'name' => 'department_id',
            'type' => 'select',
            'options' => Department::drop()
        ];
        return $filters;
    }

    public static function saveToStock($input, User $user) 
    {
        if ($input['status'] == "-- Statut --") {
            throw new Exception("Statut non selectionné");
        }
        $input['author_id'] = $user->id;
        $input['status_date'] = Carbon::now()->format("Y-m-d");
        $qty = (int) $input['qty'];
        $detail = DetailNeed::create($input);
        if ($input['status'] == 'acheté') {
            for ($i = 0; $i < $qty; $i++) {
                $item = Item::create(['code' => Str::uuid().substr(0, 5), 'item_type_id' => $input['item_type_id']]);
                $itemMovement = ItemMovement::create(['location_id' => 1, 'item_status_id' => 1, 'item_id' => $item->id]);
            }
        }
        // if ($input['status'] == 'acheté') {
        //     $itemMovements = [];
        //     $items = [];
        //     for ($i = 0; $i < $qty; $i++) {
        //         $item = ['code' => Str::uuid().substr(0, 5), 'item_type_id' => $input['item_type_id'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
        //         $items[] = $item;
        //         // $itemMovement = new ItemMovement(['location_id' => 1, 'item_status_id' => 1, 'item_id' => $item->id]);
        //     }
        //     DB::table('items')->upsert($items, ['code'], ['code']);
        // }
        return $detail;
    }

    public static function countItemPurchaseConfirmed() {
        $validated = self::$_VALIDATED;
        $details = DetailNeed::with(['itemType'])
            ->selectRaw("SUM(IF(status='validé', qty, IF(status='acheté', -qty, 0))) as qty, status, item_type_id")
            ->groupBy('item_type_id')
            ->get();
        $details = $details->filter(function($value, $key) {
            return $value['qty'] > 0;
        });
        return $details;
    }
}
