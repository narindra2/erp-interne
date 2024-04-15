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
        'method'
    ];

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

    public static function savePurchase($input, $files) {
        $unitPrice = $input['unit_price'];
        $quantity = $input['quantity'];
        $itemTypeID = $input['item_type_id'];
        $unitItemID = $input['unit_item_id'];
        $input['total_price'] = self::getTotalPrice($unitPrice, $quantity);
        $input['author_id'] = Auth::id();
        $purchase = Purchase::create($input);
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
            for ($i=0; $i<$quantity; $i++) {
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
        }, $unitPrice, $quantity, $itemTypeID, $unitItemID);
        PurchaseDetail::upsert($dataPurchaseDetail, ['unit_price'], ['unit_price']);
        ItemMovement::upsert($dataItemMovement, ['item_id'], ['item_id']);
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
