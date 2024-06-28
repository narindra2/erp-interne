<?php

namespace App\Http\Controllers;

use App;
use Auth;
use Exception;
use App\Models\Item;
use App\Models\User;
use App\Models\ItemType;
use App\Models\Purchase;
use App\Models\UnitItem;
use App\Models\DetailNeed;
use App\Models\PurchaseFile;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use App\Http\Requests\PurchaseRequest;
use App\Models\Menu;
use App\Models\PurchaseNumInvoiceLine;


class PurchaseController extends Controller
{
    public function index()
    {
        $auth_user = Auth::user();
        if (!Menu::_can_access_purchase($auth_user)) {
            abort(401);
        }
        $can_create_new_purchase =  $auth_user->isCompta() || $auth_user->isRhOrAdmin();
        return view('purchases.index', ["basic_filter" => Purchase::createFilter(), "can_create_new_purchase" => $can_create_new_purchase]);
    }

    public function getPurchaseList(Request $request)
    {
        $data = [];
        $auth = Auth::user();
        $purchases = Purchase::with(['author', 'files', "details.article" , "itemsInStock"])->getDetails($request->all())->get();
        foreach ($purchases as  $purchase) {
            if ($purchase->tagged_users) {
                /** If auth is not admin and not the specifique tagged don't add it */
                if (!$auth->isRhOrAdmin() && !in_array($auth->id, explode(",", $purchase->tagged_users))) {
                    continue;
                }
            }
            $data[] =  $this->_make_row($purchase);
        }
        return ["data" =>  $data];
    }

    public function _make_row(Purchase $purchase)
    {
        $num_purchase = $purchase->getNumPurchase();
        $detail = modal_anchor(url('/purchases/demande-form'), 'Détail <i class="fas fa-external-link-alt mb-1"></i> ', ['title' => "Détail de la demande  d'achat : $num_purchase", 'class' => 'btn btn-link btn-color-info', "data-modal-lg" => true, "data-post-purchase_id" => $purchase->id]);
        $itemsName = $purchase->details->pluck("article")->implode("name", ", ");
        $sortItemsName = str_limite($itemsName, 15);
        $items = modal_anchor(url('/purchases/demande-form'), $sortItemsName, ['title' => "Détail de la demande d'achat : $num_purchase ", 'class' => 'btn btn-link btn-color-dark', "data-modal-lg" => true, "data-post-purchase_id" => $purchase->id]);
        $statusInfo = Purchase::getPurchaseStatusInfo($purchase->status);
        $statusText = get_array_value($statusInfo, "text");
        $statusColor = get_array_value($statusInfo, "color");
        $statusColor = get_array_value($statusInfo, "color");
        $stockAction = '<span class="to-link" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="En attente d\'achat fait" ><i class="fas fa-info-circle"></i></span>';
        $progressStock = '';
        if ($purchase->status == Purchase::REFUSED_PURCHASE || !Menu::_can_access_stock(Auth::user()) ) {
            $stockAction = '<i class="fas fa-lock"></i>';
        }elseif($purchase->status == Purchase::PURCHASED_PURCHASE) {
            $stockAction = modal_anchor(url('/purchases/to-stcok-modal-form'), 'Stock <i class="fas fa-dolly-flatbed mb-1"></i> ', ['title' => "Mise en stock de la demande d'achat : $num_purchase", 'class' => 'btn btn-link btn-color-dark', "data-modal-lg" => true, "data-post-purchase_id" => $purchase->id]);
            $real_quantity = 0;
            foreach ($purchase->details as $one_detail ) {
                if ($one_detail->article->sub_category == ItemType::IMMOBILISATION) {
                    $real_quantity += $one_detail->quantity;
                }
            }
            $item_already_in_stock = $purchase->itemsInStock->count();
            $rest_to_migrate_in_stock = $real_quantity - $item_already_in_stock;
            
            if ($rest_to_migrate_in_stock) {
                $progress = round($item_already_in_stock * 100 / $real_quantity); 
                $progressbar = " <div class='progress-bar bg-primary' role='progressbar' style='width:$progress%' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100'></div>";
                $progressStock =  '<div class="progress h-6px w-100 me-2 bg-light-primary">'. $progressbar . '</div>';
            }elseif ($rest_to_migrate_in_stock == 0) {
                $progressbar = " <div class='progress-bar bg-success' role='progressbar' style='width:100%' aria-valuenow='10' aria-valuemin='0' aria-valuemax='100'></div>";
                $progressStock =  '<div class="progress h-6px w-100 me-2 bg-success">'. $progressbar . '</div>';
                $stockAction = modal_anchor(url('/purchases/to-stcok-modal-form'), 'Stock <i class="fas fa-check-circle"></i>', ['title' => "Mise en stock fait : $num_purchase", 'class' => 'btn btn-link btn-color-success', "data-modal-lg" => true, "data-post-purchase_id" => $purchase->id]);
            }else{
                $progressbar = " <div class='progress-bar bg-dark' role='progressbar' style='width:10%' aria-valuenow='10' aria-valuemin='0' aria-valuemax='100'></div>";
                $progressStock =  '<div class="progress h-6px w-100 me-2 bg-dark">'. $progressbar . '</div>';
            }
        }
        return [
            "DT_RowId" => row_id("purchases", $purchase->id),
            'num' =>$num_purchase ,
            'info' => "<span data-kt-element='bullet' class='bullet bullet-vertical d-flex align-items-center min-h-30px  bg-$statusColor'></span>",
            'date' => $purchase->purchase_date->format("d-M-Y"),
            'author' => $purchase->author->sortname,
            'method' => "<span class='badge badge-sm badge-light-info'>$purchase->method</span>",
            'items' => $items,
            'total_price' => "<h5>$purchase->total_price</h5>" . env("CURRENCY"),
            // 'files' => $this->createColumnFiles($purchase),
            'status' => "<span class='badge badge-sm mb-3 badge-$statusColor'>$statusText</span>" ,
            'created_at' => convert_to_real_time_humains($purchase->created_at, 'd-M-Y' , false),
            'delete' =>  js_anchor('<i class="fas fa-trash " style="font-size:12px" ></i>', ["data-action-url" => url("/purchases/delete"), "data-post-purchase_id" => $purchase->id, "class" => "btn btn-sm btn-clean ", "title" => "Supprimé", "data-action" => "delete"]),
            'actions' => $detail,
            'stock' => $stockAction . " " .  $progressStock
        ];
    }

    public function createColumnFiles($purchase)
    {
        if ($purchase->files->count()) {
            return view('purchases.columns.files', ['files' => $purchase->files])->render();
        }
        return "";
    }
    public function modal_form(Request $request)
    {
        $data  = [];
        $purchase_model = Purchase::with(["files", "details", "author"  , "numInvoiceLines"])->find($request->purchase_id) ?? new Purchase();
        $data['purchase_model'] = $purchase_model;
        $data['units'] = UnitItem::whereDeleted(false)->get();
        $data['itemTypes'] = ItemType::whereDeleted(0)->orderBy('name', 'desc')->get();
        $data['auth'] = Auth::user();
        /** Tag user */
        $users = User::with(["userJob.job"])->whereDeleted(0)->where("users.id", "<>", (Auth::id() == $purchase_model->author_id)   ? Auth::id()  : 0)->get();
        foreach ($users as $user) {
            $data['users'][] = $this->_make_user_tag($user);
        }
        $data['defaut_tagged'] = [];
        $tagged_users = $users->whereIn("id", explode(",", $purchase_model->tagged_users));
        if ($purchase_model->tagged_users) {
            foreach ($tagged_users as $tagged_user) {
                $data['defaut_tagged'][] = $this->_make_user_tag($tagged_user);
            }
        }
        $data['defaut_tagged'] = json_encode($data['defaut_tagged']);
        return view('purchases.modal-form', $data);
    }
    private function _make_user_tag($user): array
    {
        return [
            "value" => $user->id,
            "name" => $user->sortname,
            "avatar" => $user->avatarUrl,
            "job" => ($user->userJob && $user->userJob->job) ? $user->userJob->job->name : "",
        ];
    }

    public function save(PurchaseRequest $request)
    {
        if (!Menu::_can_access_purchase(Auth::user())) {
            return ['success' => false, 'message' => "Vous n 'avez pas l'accès"];
        }
        try {
            if ($request->purchase_id &&  $request->is_update == "true") {
                PurchaseDetail::where("purchase_id", $request->purchase_id)->delete();
            }
        } catch (\Throwable $th) {
        }
        try {
            Purchase::savePurchase($request->all(), $request->file("files"));
            return ['success' => true, 'message' => "Demande d'achat bien " . ($request->purchase_id ? 'sauvegardée' :  'ajoutée')];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function delete(Request $request)
    {
        if (!Menu::_can_access_purchase(Auth::user())) {
            return ['success' => false, 'message' => "Vous n 'avez pas l'accès"];
        }
        $purchase = Purchase::find($request->purchase_id);
        if ($request->input("cancel")) {
            $purchase->update(["deleted" => 0]);
            PurchaseDetail::where("purchase_id", $purchase->id)->update(["deleted" => 0]);
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row($purchase)];
        } else {
            $purchase->update(["deleted" => 1]);
            PurchaseDetail::where("purchase_id", $purchase->id)->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
    public function saveNumInvoiceLine(Request $request)
    {
        PurchaseNumInvoiceLine::updateOrCreate(
            ["purchase_id" => $request->purchase_id , "num_invoice"   => $request->old_num_invoice ??  $request->new_num_invoice ],
            ["purchase_id" => $request->purchase_id , "num_invoice"   => $request->new_num_invoice ],
        );
        return ['success' => true, 'message' => "Sauvegarder avec success" ];
    }
    public function deleteNumInvoiceLine(Request $request)
    {
        PurchaseNumInvoiceLine::where("id" ,$request->purchase_num_invoice_id)->update(["deleted" => 1]);
        return ['success' => true, 'message' => "Suppression avec success" ];
    }
    public function downloadFile(PurchaseFile $purchaseFile)
    {
        $url = storage_path($purchaseFile->src);
        return response()->download($url);
    }
    public function migrationToStockModal(Request $request)
    {
        
        $purchase_model = Purchase::with(["details.article", "numInvoiceLines" ,"itemsInStock"])->find($request->purchase_id);
        return view("purchases.modal-form-migration-stock", ["purchase_model" => $purchase_model]);
    }
}
