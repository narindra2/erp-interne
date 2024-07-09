<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'place',
        'item_id',
        'user_id'
    ];
    public function item() {
        return $this->belongsTo(Item::class, "item_id");
    }

    public function location() {
        return $this->belongsTo(Location::class, "location_id");
    }

    public function getUsersAssigned() {
        $userJobs = get_users_cache();
        $userIDs = explode(",", $this->user_id);
        $users = collect();
        foreach ($userIDs as $userID) {
            $userJobs->each(function($userJob, $key) use ($userID, $users) {
                if ($userJob->users_id == $userID) {
                    $users->push($userJob->user);
                    return false;
                }
            });
        }
        return $users;
    }

    public function isUsedByUser($userID) {
        $tab = explode(",", $this->user_id);
        return in_array($userID, $tab);
    }

    public function scopeGetLastAssignationOfItems($query) {
        return ItemMovement::with(["item.type", "location"])
            ->selectRaw("*")->from(self::maxCreatedAt_subQuery())
            ->join("item_movements", function($join) {
                $join->on("created_at_max", "=", "created_at");
                $join->on("item_id_max", "=", "item_id");
            })
            ->where("location_id", "<>", Location::$_ID_STOCK)
            ->get();
    }

    public static function saveAssignationOfUsers($input) {
        $itemMovement = ItemMovement::find($input['id']);
        $itemMovement->user_id = implode(",", $input['users']);
        $itemMovement->save();
        return $itemMovement;
    }

    public static function getHistoricItem(Item $item) {
        return ItemMovement::where("item_id", $item->id)->whereDeleted(0)->get();
    }

    public static function getItemsOnStock($status=null) {
        if (!$status) $status = ItemStatus::$_OK;
        return ItemMovement::with(["item.type"])->selectRaw('*')
            ->from(self::maxCreatedAt_subQuery())
            ->join('item_movements', function($join) {
                $join->on("created_at_max", "=", "created_at");
                $join->on("item_id_max", "=", "item_id");
            })
            ->whereNull("user_id")
            ->where("location_id", Location::$_ID_STOCK)
            ->where('item_status_id', $status)
            ->get()
            ->groupBy("item.type.name");
    }

    public static function countItemsOnStock($input) {
        $stocks = self::getItemsOnStock(get_array_value($input, "status"));
        $data  = [];
        foreach ($stocks as $name => $stock) {
            $itemType = new ItemType();
            $itemType->name = $name;
            $itemType->count = $stock->count();
            $data[] = $itemType; 
        }
        return collect($data);
    }

    public static function maxCreatedAt_subQuery() {
        $query = ItemMovement::query();
        return $query->selectRaw("MAX(created_at) as created_at_max, item_id as item_id_max")
            ->whereDeleted(0)
            ->groupBy('item_id');
    }

    public static function filterStock() {
        $filters = [];
        $itemStatusOptions = ItemStatus::convertToSelect();
        $filters[] = [
            "label" => "Statut",
            "name" => "status",
            "type" => "select",
            "options" => $itemStatusOptions
        ];
        return $filters;
    }
}
