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


class PurchaseController extends Controller
{
    public function index() {
        return view('purchases.index');
    }

    public function getPurchaseList() {
        $data = [];
        $purchases = Purchase::with(['author', 'files',"details.itemType"])->whereDeleted(0)->latest()->get();
        foreach ($purchases as  $purchase) {
            $data[] =  $this->_make_row( $purchase);
        }
        return ["data" =>  $data];
    }

    public function _make_row( Purchase $purchase)
    {
        $detail = modal_anchor(url('/purchases/demande-form'), 'Détail <i class="fas fa-external-link-alt mb-1"></i> ', ['title' => "Détail demande d'achat", 'class' => 'btn btn-link btn-color-info' , "data-modal-lg" => true , "data-post-purchase_id" =>$purchase->id]);
        $itemsName = $purchase->details->pluck("itemType")->implode("name",", ");
        $sortItemsName=str_limite($itemsName ,15);
        $items = modal_anchor(url('/purchases/demande-form'), $sortItemsName, ['title' => "Detail demande d'achat", 'class' => 'btn btn-link btn-color-dark' , "data-modal-lg" => true , "data-post-purchase_id" =>$purchase->id]);
        $statusInfo = Purchase::getPurchaseStatusInfo($purchase->status);
        $statusText = get_array_value($statusInfo,"text");
        $statusColor = get_array_value($statusInfo,"color");
        $statusColor = get_array_value($statusInfo,"color");
        if ($purchase->status == Purchase::PURCHASED_PURCHASE) {
          
        }
        
        return [
            'info' => "<span data-kt-element='bullet' class='bullet bullet-vertical d-flex align-items-center min-h-30px  bg-$statusColor'></span>",
            'date' => $purchase->purchase_date->format("d-M-Y"),
            'author' => $purchase->author->sortname,
            // 'method' => "<span class='badge badge-sm badge-info'></span>" ,
            'method' => "<span class='badge badge-sm badge-light-info'>$purchase->method</span>" ,
            'items' => $items ,
            'total_price' => "<h5>$purchase->total_price</h5>" . env("CURRENCY"),
            'files' => $this->createColumnFiles($purchase),
            'status' =>"<span class='badge badge-sm badge-$statusColor'>$statusText</span>" ,
            'created_at' => convert_to_real_time_humains($purchase->created_at),
            'actions' => $detail
        ];
    }

    public function createColumnFiles($purchase) {
        if ($purchase->files->count()) {
            return view('purchases.columns.files', ['files' => $purchase->files])->render();
        }
        return "";
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
