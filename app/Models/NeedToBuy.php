<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeedToBuy extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type_id',
        'nb',
        'author_id',
        'is_solved',
        'status',
        'ticket_id',
        'department_id',
        'unit_item_id'
    ];

    public static $_STATUSES = ["normal", "urgent", "bas", "resolu"];

    public function details() {
        return $this->hasMany(DetailNeed::class, "need_to_buy_id");
    }

    public function department() {
        return $this->belongsTo(Department::class, "department_id");
    }

    public function unit() {
        return $this->belongsTo(UnitItem::class, 'unit_item_id');
    }

    public function getStatusHtmlAttribute() {
        return view('need-to-buy.columns.status', ['status' => $this->status, "class" => $this->getStatusHtmlClass()])->render();
    }

    public function getStatusHtmlClass() {
        $class = "primary";
        if ($this->status == "urgent")          $class = "danger";
        else if ($this->status == "bas")        $class = "warning";
        else if ($this->status == "resolu")     $class = "success";
        return $class;
    }                                                

    public function getNumTicket() {
        if ($this->ticket_id) {
            return modal_anchor(url("/ticket/edit/$this->ticket_id"), "#00$this->ticket_id", ['title' => trans('lang.edit'), 'data-modal-lg' => true, 'data-post-clearBtn' => true]);
        }
        return null;
    }

    public function getDepartmentName() {
        if ($this->department_id) {
            return $this->department->name;
        }
        return "-----";
    }

    public function author() {
        return $this->belongsTo(User::class, "author_id");
    }

    public function itemType() {
        return $this->belongsTo(ItemType::class, "item_type_id");
    }

    public function getUnitPriceAttribute() {
        return $this->itemType->unit_price;
    }

    public function getTotalPriceAttribute() {
        return $this->unit_price * $this->nb;
    }

    public static function saveNeed(Ticket $ticket, $needToBuyIDs, $itemTypeIDs, $quantities, $unitIDs) {
        $authorID = Auth::id();
        for ($i=0; $i<count($itemTypeIDs); $i++) {
            $id = null;
            if ($needToBuyIDs != null) {
                $id = $needToBuyIDs[$i];
            }
            NeedToBuy::updateOrcreate([
                'id' => $id
            ],[
                'item_type_id' => $itemTypeIDs[$i],
                'nb' => $quantities[$i],
                'author_id' => $authorID,
                'ticket_id' => $ticket->id,
                'unit_item_id' => $unitIDs[$i]
            ]); 
        }
    }

    public static function findNeedToBuy($input) {
        $needToBuy = NeedToBuy::with(['author', 'itemType', 'department', 'unit']);
        $status = get_array_value($input, "status");
        if ($status) {
            $needToBuy->where("status", $status);
        }
        $department_id = get_array_value($input, "department_id");
        if ($department_id) {
            $needToBuy->where("department_id", $department_id);
        }
        return $needToBuy->whereDeleted(0)->get();
    }

    public function countItemToBuy($status="validé") {
        return $this->details->where("status", $status)->count();
    }

    public function getTotalPrice($status="validé") {
        return $this->unit_price * $this->countItemToBuy($status);
    }

    public static function createFilter() {
        $filters = [];
        $filters[] = [
            'label' => 'Statut',
            'name' => 'status',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Bas',
                    'value' => 'bas'
                ],
                [
                    'text' => 'Normal',
                    'value' => 'normal'
                ],
                [
                    'text' => 'Urgent',
                    'value' => 'urgent'
                ],
                [
                    'text' => 'Resolu',
                    'value' => 'resolu'
                ],
            ]
        ];
        $filters[] = [
            'label' => 'Departement',
            'name' => 'department_id',
            'type' => 'select',
            'options' => Department::drop()
        ];
        return $filters;
    }
}