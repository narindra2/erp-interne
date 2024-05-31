<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use App\Models\User;
use App\Models\ItemType;
use App\Models\Purchase;
use App\Models\UnitItem;
use App\Models\DetailNeed;
use App\Models\PurchaseFile;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\PurchaseListResource;
use App\Http\Resources\PurchaseDetailResource;

class PurchaseController extends Controller
{
    public function index() {
        return view('purchases.index');
    }

    public function getPurchaseList() {
        $purchases = Purchase::with(['author', 'files',"details.itemType"])->whereDeleted(0)->latest()->get();
        return PurchaseListResource::collection($purchases);
    }

    public function modal_form(Request $request) {
        $data = [];
        $data['purchase_model'] = Purchase::with(["files" , "details","author"])->find($request->purchase_id) ?? new Purchase();

        $data['units'] = UnitItem::whereDeleted(false)->get();
        $data['itemTypes'] = ItemType::whereDeleted(0)->orderBy('name', 'desc')->get();
        $data['auth'] = Auth::user();;
        // $data['users'] = User::select("id","deleted","name", "firstname")->where("id" , "<>" ,$data['auth']->id)->whereDeleted(0)->get();
        return view('purchases.modal-form', $data);
    }
    public function form() {
        $data = [];
        $data['needs'] = DetailNeed::countItemPurchaseConfirmed();
        $totalNeedsPrice = 0;
        foreach ($data['needs'] as $need) {
            $totalNeedsPrice += $need->total_price;
        }
        $data['totalNeedsPrice'] = $totalNeedsPrice;
        $data['units'] = UnitItem::whereDeleted(0)->get();
        $data['itemTypes'] = ItemType::whereDeleted(0)->orderBy('name', 'desc')->get();
        return view('purchases.form', $data);
    }

    public function save(PurchaseRequest $request) {
        try {
           
            if ($request->purchase_id &&  $request->is_update == "true") {
                PurchaseDetail::where("purchase_id",$request->purchase_id)->delete();
            }  
        } catch (\Throwable $th) {
        } 
        
        try {
            Purchase::savePurchase($request->all(), $request->file("files"));
            return ['success' => true, 'message' =>"Demande d'achat bien " . ($request->purchase_id ? 'sauvegardée' :  'ajoutée') ];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function pageDetail(Purchase $purchase) {
        $purchase->load('details.itemType');
        $purchase->load('details.unit');
        return view('purchases.detail', compact('purchase'));
    }

    public function downloadFile(PurchaseFile $purchaseFile) {
        $url = storage_path($purchaseFile->src);
        return response()->download($url);
    }
}
