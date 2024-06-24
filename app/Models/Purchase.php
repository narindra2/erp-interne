<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\TicketJobNotification;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\NewPurcahseNotification;
use App\Notifications\UpdateStatusPurchseNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;
    protected $table = "purchases";
    protected $casts = [
        'purchase_date' => 'date'
    ];

    protected $fillable = [
        'purchase_date',
        'total_price',
        'author_id',
        'note',
        'status',
        'method',
        'deleted',
        'tagged_users',
    ];
    const INPROGRESS_PURCHASE = "in_progress";
    const VALIDATED_PURCHASE = "validated";
    const PURCHASED_PURCHASE = "purchased";
    const REFUSED_PURCHASE = "refused";

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function getNumPurchase() {
        return "#" . $this->id;
    }

    public function details() {
        return $this->hasMany(PurchaseDetail::class, "purchase_id");
    }

    public function numInvoiceLines() {
        return $this->hasMany(PurchaseNumInvoiceLine::class, "purchase_id")->whereDeleted(0);
    }
    public function files() {
        return $this->hasMany(PurchaseFile::class, "purchase_id");
    }
    public function itemsInStock() {
        return $this->hasMany(Item::class, "purchase_id");
    }
    // public function dateHTML() {
    //     return $this->purchase_date->translatedFormat('d M Y');
    // }
    public static function purchaseStatusList() {
       return [
        ["value" => null,"text" => "Statut" ],
        ["value" => self::INPROGRESS_PURCHASE , "text" => "En attente" ,  "color" => "primary"],
        ["value" => self::VALIDATED_PURCHASE , "text" => "Validé" ,  "color" => "success"],
        ["value" => self::PURCHASED_PURCHASE , "text" => "Achat fait" ,  "color" => "info"],
        ["value" => self::REFUSED_PURCHASE , "text" => "Refusé" ,  "color" => "danger"],
       ];
    }
    public static  function getPurchaseStatusInfo($purchase_status = "") {
        return collect(self::purchaseStatusList())->firstWhere("value","=" , $purchase_status);
    }
    public   function getUserNotification() {
        if ($this->tagged_users) {
            $tagged_users = User::select(["id" ,"name","firstname","deleted"])
                                  ->whereDeleted(0)
                                  ->whereIn("id", explode("," ,$this->tagged_users))->get();
            return $tagged_users->push($this->author);
        }
        /**RH user + Admin user  */
        if ($this->author->isRhOrAdmin()) {
            return get_cache_rh_admin();
        }else{
            /**RH user + Admin user + user not adminitratif  */
            return get_cache_rh_admin()->push($this->author);
        }
    }

    public static function savePurchase($input, $files) {
        $auth = Auth::user();
        $unitPrice = $input['unit_price'];
        $quantity = $input['quantity'];
        $itemTypeID = $input['item_type_id'];
        $unitItemID = $input['unit_item_id'];
        $propriety = $input['proprieties'];
        /** Remove the first becuase it ith a fake data from front */
        array_shift($unitPrice );
        array_shift( $quantity ) ;
        array_shift($itemTypeID);
        array_shift( $unitItemID );
        array_shift( $propriety );

        $tagged_users = $input['users'] ? collect(json_decode($input['users'], true))->implode("value",",") : null;
        $input['tagged_users'] =   $tagged_users ;

        $input['total_price'] = self::getTotalPrice($unitPrice, $quantity);
        
        /** Create  options */
        if (!isset($input['purchase_id'])) {
            $input['author_id'] = $auth->id;
            $input['status'] = self::INPROGRESS_PURCHASE;
        }else{
            $old_purchase = Purchase::with(['author', 'files',"details.itemType"])->find($input['purchase_id']);
        }
        $input['purchase_date'] = convert_date_to_database_date( $input['purchase_date']);
        $purchase = Purchase::updateOrCreate(["id" => ($input['purchase_id'] ?? null) ],$input);
        $purchase->saveFiles($files);
      
        $dataPurchaseDetail = [];
        $dataItemMovement = [];
        array_map(function($unitPrice, $quantity, $itemTypeID, $unitItemID,$propriety) use ($purchase, &$dataPurchaseDetail, &$dataItemMovement) {
            $dataPurchaseDetail[] = [
                'item_type_id' => $itemTypeID,
                'purchase_id' => $purchase->id,
                'quantity' => $quantity,
                'unit_item_id' => $unitItemID,
                'unit_price' => $unitPrice,
                'propriety' => $propriety
            ];
        }, $unitPrice, $quantity, $itemTypeID, $unitItemID,$propriety);
        PurchaseDetail::upsert($dataPurchaseDetail, ['unit_price'], ['unit_price']);
        if (!isset($input['purchase_id'])) {
            dispatch(new TicketJobNotification(  $purchase->getUserNotification(), new NewPurcahseNotification( $purchase ,  $auth)))->afterResponse();
        }else{
            if ($old_purchase->status != $purchase->status) {
                $changed["old_status"] = Purchase::getPurchaseStatusInfo($old_purchase->status)["text"] ;
                $changed["new_status"] =  Purchase::getPurchaseStatusInfo($purchase->status)["text"];
                /** Send the new status of new purchase */
                dispatch(new TicketJobNotification(  $purchase->getUserNotification(), new UpdateStatusPurchseNotification( $purchase  ,  $auth , $changed)))->afterResponse();
            }
        }
    }

    public static function getTotalPrice($unitPrice, $quantity) {
        $totalPrice = 0;
        array_map(function($unitPrice, $quantity) use (&$totalPrice) {
            $totalPrice += $unitPrice * $quantity;
        }, $unitPrice, $quantity);
        return $totalPrice;
    }

    public function saveFiles($files) {
        if ($files != null) {
            foreach($files as $file) {
                //Save the file to the server
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads', $fileName, 'public');
                //write the filepath to the database
                PurchaseFile::create([
                    'purchase_id' => $this->id,
                    'src' => 'app/public/' . $filePath,
                    'filename' => $file->getClientOriginalName()
                ]);
            }
        }
    }
    public function scopeGetDetails($query , $options = []) {
        $auth = Auth::user();
        $status  = get_array_value($options, "status");
        if ($status) {
            $query->where("status", $status);
        }
        $method  = get_array_value($options, "method");
        if ($method) {
            $query->where("method", $method);
        }
        if ($method) {
            $query->where("method", $method);
        }
        if (!$auth->isRhOrAdmin()) {
            $query->whereRaw('FIND_IN_SET("' . $auth->id . '", tagged_users)');
        }
        return $query->whereDeleted(0)->latest();
    }
    public static function createFilter($options = [])
    {
        $filters = []  ;
        $filters[] = [
            "label" => "Statut", 
            "name" =>"status",
            "type" => "select",
            "width"  =>"w-200px",
            "disabled_first" => true,
            'attributes' => [
                "data-hide-search" => "true",
                "data-allow-clear" => "true",
            ],
            "options" =>  self::purchaseStatusList(),
        ];
        $filters[] = [
            "label" => "Méthode", 
            "name" =>"method",
            "type" => "select",
            "width"  =>"w-200px",
            "disabled_first" => true,
            'attributes' => [
                "data-hide-search" => "true",
                "data-allow-clear" => "true",
            ],
            "options" => [
                ["value" => null, "text" => "Méthode"],
                ["value" => "Espèce", "text" => "Espèce"],
                ["value" => "Chèque", "text" => "Chèque"],
                ["value" => "Carte (VISA)" , "text" => "Carte (VISA)" ],
                ["value" => "Carte (MASTERCARD)", "text" => "Carte (MASTERCARD)"],
            ],
        ];
        return $filters;
    }
}
