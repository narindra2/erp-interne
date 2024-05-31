<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
    const VALIDATED_PURCHASE = "valided";
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

    public static function savePurchase($input, $files) {
        $unitPrice = $input['unit_price'];
        $quantity = $input['quantity'];
        $itemTypeID = $input['item_type_id'];
        $unitItemID = $input['unit_item_id'];

        array_shift($unitPrice );
        array_shift( $quantity ) ;
        array_shift($itemTypeID);
        array_shift( $unitItemID );
       
        $input['total_price'] = self::getTotalPrice($unitPrice, $quantity);
        $input['author_id'] = Auth::id();
        
        if (!isset($input['purchase_id'])) {
            $input['status'] = self::INPROGRESS_PURCHASE;
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
