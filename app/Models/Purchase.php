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

    protected $casts = [
        'purchase_date' => 'date'
    ];

    protected $fillable = [
        'purchase_date',
        'total_price',
        'author_id',
        'note',
        'status',
        'method'
    ];
    const INPROGRESS_PURCHASE = "in_progress";
    const VALIDATED_PURCHASE = "validated";
    const PURCHASED_PURCHASE = "purchased";
    const REFUSED_PURCHASE = "refused";

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function details() {
        return $this->hasMany(PurchaseDetail::class, "purchase_id");
    }

    public function files() {
        return $this->hasMany(PurchaseFile::class, "purchase_id");
    }

    public function getDetails(Request $request) {
        if (!$this->id) {
            return $request->session()->get("purchaseDetail");
        }
        $this->load('details.itemType');
        return $this->details; 
    }

    public function dateHTML() {
        return $this->purchase_date->translatedFormat('d M Y');
    }
    public static function purchaseStatusList() {
       return [
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

        array_shift($unitPrice );
        array_shift( $quantity ) ;
        array_shift($itemTypeID);
        array_shift( $unitItemID );
       
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
        array_map(function($unitPrice, $quantity, $itemTypeID, $unitItemID) use ($purchase, &$dataPurchaseDetail, &$dataItemMovement) {
            $dataPurchaseDetail[] = [
                'item_type_id' => $itemTypeID,
                'purchase_id' => $purchase->id,
                'quantity' => $quantity,
                'unit_item_id' => $unitItemID,
                'unit_price' => $unitPrice
            ];
            /*
            for ($i = 0; $i < $quantity; $i++) {
                $item = Item::create([
                    'item_type_id' => $itemTypeID,
                    'code' => "achat-" . $purchase->created_at->format('Y-m-d')
                ]);
                $dataItemMovement[] = [
                    'location_id' => 1,
                    'item_status_id' => 1, 
                    'item_id' => $item->id
                ];
            }
            */
        }, $unitPrice, $quantity, $itemTypeID, $unitItemID);
        PurchaseDetail::upsert($dataPurchaseDetail, ['unit_price'], ['unit_price']);
        // ItemMovement::upsert($dataItemMovement, ['item_id'], ['item_id']);
        if (!isset($input['purchase_id'])) {
            dispatch(new TicketJobNotification(  $purchase->getUserNotification(), new NewPurcahseNotification( $purchase ,  $auth)));
        }else{
            if ($old_purchase->status != $purchase->status) {
                /** Send the new status of new purchase */
                dispatch(new TicketJobNotification(  $purchase->getUserNotification(), new UpdateStatusPurchseNotification( $purchase  ,  $auth)));
            }
        }
        return $purchase;
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
}
