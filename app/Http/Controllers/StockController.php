<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use App\Models\Item;
use App\Models\Menu;
use App\Models\User;
use App\Models\ItemType;
use App\Models\Location;
use App\Models\Purchase;
use App\Models\ItemCategory;
use App\Models\ItemMovement;
use Illuminate\Http\Request;
use App\Http\Requests\ItemTypeRequest;
use App\Models\PurchaseNumInvoiceLine;
use App\Http\Requests\CreateItemCategoryResquet;

class StockController extends Controller
{
    public function index() {
        $auth_user = Auth::user();
        if (!Menu::_can_access_stock($auth_user)) {
            abort(401);
        }
        return view('stock.index');
    }
    /** Tabs */
    public function inventory() {
        return view('stock.tabs.inventory' , ["basic_filter" => Item::createFilter()]);
    }
    public function article() {
        return view('stock.tabs.articles');
    }

    public function category() {
        return view('stock.tabs.category');
    }
    public function location() {
        return view('stock.tabs.location');
    }
    /** End tabs */

    /** Artilce inventory gestion */
    public function inventory_data_list(Request $request) {
        $data = [] ; $req = $request->all();
        $query = Item::with(["article.category","purchase","num_invoice"])->whereDeleted(0);
        $etat = get_array_value( $req,"etat_item");
        if ($etat) {
            $query->where("etat", $etat);
        }
        $cat_id = get_array_value( $req,"cat_id");
        if ($cat_id) {
            $query->whereRelation("article", "category_id",$cat_id);
        }
        $sub_cat = get_array_value( $req,"sub_cat");
        if ($sub_cat) {
            $query->whereRelation("article", "sub_category",$sub_cat);
        }
        $article_id = get_array_value( $req,"article_id");
        if ($article_id) {
            $query->whereRelation("article", "id",$article_id);
        }
        $items = $query->latest()->get();
        foreach ($items as $item) {
            $data[] = $this->_make_row_inventory( $item);
        }
        return ["data"  => $data];
    }
    public function _make_row_inventory(Item $item) {
        $row["DT_RowId"] = row_id("invetory", $item->id);
        $row["qrcode"]  =   view("stock.article.article-qrcode-column",["item" => $item])->render();
        $row["code"] =  modal_anchor(url("/stock/inventory/modal-form"), "<span class='text-info fs-5'>{$item->code_detail}</span>", ["title" => "Edition du " . $item->code_detail , "data-post-item_id" => $item->id]); ;
        $row["name"] = "<span class='text-dark fs-5 fw-bold'>{$item->article->name}</span>" ;
        $row["propriety"] = $item->propriety ? "<span class='text-gray-700 fw-semibold d-block fs-7'>{$item->propriety}</span>"  :"-";
        $row["sub_cat"] = $item->article->sub_category;
        $row["cat"] =  ($item->article && isset($item->article->category) )  ? $item->article->category->name : "-" ;
        $row["purchase"] = $row["num_invoice"] = "";
        if ($item->purchase_id) {
            $num =  $item->purchase->getNumPurchase();
            $row["purchase"] = modal_anchor(url('/purchases/demande-form'), "$num <i class='fas fa-link'></i> ", ['title' => "Détail de la demande  d'achat : $num ", 'class' => 'btn btn-link btn-color-info', "data-modal-lg" => true, "data-post-purchase_id" => $item->purchase_id]);
        }
        $row["location"]  = $item->get_actualy_place();
        $row["assigned"]  = $item->get_user_use_it();
        $row["disponiblity"]  = $item->get_disponible();
        if ($row["disponiblity"] == "En usage") {
            $row["disponiblity"]  .=  " : " . $row["assigned"];
        }
        
        /*** Relationship  in num_invoice */
        if ($item->num_invoice) {
            $row["num_invoice"] = $item->num_invoice->num_invoice;
        }else{
            /*** num_invoice add manuely */
            if($item->num_invoice_id){
                $row["num_invoice"] = $item->num_invoice_id;
            }
        }
        $row["num_invoice"] = $row["num_invoice"] ." ". $row["purchase"];
        $etat_info = $item->getEtatInfo();
        $etat_class = $etat_info["color"];
        $etat_text = $etat_info["text"];
        $row["etat"] = "<span class='badge badge-$etat_class '> $etat_text </span>" ;
        $row["date"] = $item->date->format("d-m-Y");
        $row["prix_ht"] = $item->price_ht ? "<span class='badge badge-light-dark '>$item->price_ht Ar</span>"  :  "-" ;
        // $row["prix_htt"] = $item->price_htt ? "<span class='badge badge-light-dark '>$item->price_htt Ar</span>"  :  "-" ;
        $observation_sort  = str_limite($item->observation,20);
        $row["observation"] = !$item->observation  ? "-"  : "<span class='to-link' data-bs-toggle='tooltip'  data-bs-placement='top' title='{$item->observation}' > $observation_sort </span>";
        $row["detail"] =   modal_anchor(url("/stock/inventory/modal-form"), 'Détail ', ["title" => "Edition du " . $item->code_detail , "data-post-item_id" => $item->id]);
        $row["delete"] =  js_anchor('<i class="fas fa-trash me-4 "></i>', [ 'data-action-url' => url("/stock/delete/item"), "title" => "Supprimer","data-post-item_id" => $item->id , "data-action" => "delete"]);
        return $row;
    }
    public function inventor_modal_form(Request $request) {
        $item =  Item::with(["article.category" ])->find($request->item_id);
        $purchases = Purchase::with(['author' ])->whereDeleted(0)->get();
        $num_invoices = PurchaseNumInvoiceLine::whereDeleted(0)->get();
        $locations = Location::whereDeleted(0)->get();
        $users = User::whereDeleted(0)->get();
        if ($request->item_id) {
            return view('stock.article.article-in-stock-modal-form', ["item" =>$item , "purchases"  =>$purchases , "num_invoices" => $num_invoices , "locations" => $locations , "users" => $users]);
        }
    }
    public function delete_item_in_inventory_list(Request $request) {
        $item = Item::find($request->item_id);
        if ($request->input("cancel")) {
            $item->update(["deleted" => 0]);
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row_inventory($item)];
        } else {
            $item->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
    
    public function save_inventor_from_update(Request $request) {
        $data = $request->all();
        $data["date"] = convert_date_to_database_date($request->date);
        $data["num_invoice_id"] = $request->num_invoice_id == "0" ? null  : $request->num_invoice_id;
        $data["purchase_id"] = $request->purchase_id == "0" ? null  : $request->purchase_id;
        $item = Item::find($request->item_id);
        $actualy_place  =   $item->get_actualy_place_info();
        $item->update($data);
        
        /** Check if item is moved */
        if ($actualy_place)  {
            $new_assigned = $request->user_id ? $request->user_id :  ["new"];
            $old_assigned = $item->user_id ? explode("," , $item->user_id)  :  ["old"];
            if(($actualy_place->location_id != $request->location_id) || $actualy_place->place != $request->place || sort($new_assigned) === sort($old_assigned) ){
                $this->_set_new_mouvement($request);
            }
        }
        $item->refresh()->load(["article.category","purchase","num_invoice"]);
        return ['success' => true, 'message' => "Mise à jour avec succès" , "row_id" =>  row_id("invetory",$item->id )  ,"data" => $this->_make_row_inventory( $item)];
    }
    public function _set_new_mouvement(Request $request , $item_id = 0){
        if ($request->location_id) {
            $locations  = $request->only(["location_id","item_id","place"]);
            if (count($request->user_id ?? [])) {
                $locations["user_id"] = collect($request->user_id )->implode(",");
            }
            if ($item_id) {
                $locations["item_id"] = $item_id;
            }
            ItemMovement::create($locations);
        }
    }
    public function create_article_migration_to_stock(Request $request)
    {
        if (!$request->item_type_id) {
            die(json_encode(["success" => false, "validation" => true,  "message" =>  "Le champ « matériel à ajouter » ne peux pas être vide pour un nouvel enregistrement svp !"]));
        }
        $data = $request->all();
        $data["code"] = Item::generateCodeItemForNew($request->item_type_id);
        $data["date"] = convert_date_to_database_date($request->date);
        $data["num_invoice_id"] = $request->num_invoice_id == "0" ? null  : $request->num_invoice_id;
        // $article = ItemType::find($request->item_type_id);
        // $data["etat"] =  $article->sub_category == ItemType::CONSOMABLE ? "en_stock"  : "fonctionnel";
        $data["etat"] =  "fonctionnel";
        $data["created_from"] = "purchase_form"; /** From migration  form in purchase request*/
        $item =  Item::updateOrCreate( ["id" => $request->item_id ], $data);
        ItemMovement::create(["location_id" => Location::STOCK_ID , "item_id" =>  $item->id]);
        return ['success' => true, 'message' => "Sauvegarder avec succès" , "item" => $item ];
    }
    public function create_article_to_stock_modal_form(Request $request)
    {
        $articles = ItemType::whereDeleted(0)->get();
        $purchases = Purchase::with(['author'])->whereDeleted(0)->get();
        $locations = Location::whereDeleted(0)->get();
        $users = User::whereDeleted(0)->get();
        $num_invoices = PurchaseNumInvoiceLine::whereDeleted(0)->get();
        
        return view('stock.article.create-article-to-stock-modal-form', ["articles" =>$articles , "purchases"  =>$purchases , "num_invoices" => $num_invoices , "locations" => $locations , "users" => $users]);
    }
    public function save_new_article_to_stock(Request $request)
    {   
        if (!$request->item_type_id) {
           die(json_encode(["success" => false, "validation" => true,  "message" =>  "Le champ « matériel à ajouter » ne peux pas être vide pour un nouvel enreigistrement svp !"]));
        }
        $data = $request->all();
        $data["created_from"] = "inventory_form";
        $data["code"] = Item::generateCodeItemForNew($request->item_type_id);
        $data["date"] = convert_date_to_database_date($request->date);
        $data["num_invoice_id"] = $request->num_invoice_id == "0" ? null  : $request->num_invoice_id;
        $data["purchase_id"] = $request->purchase_id == "0" ? null  : $request->purchase_id;
        $item =  Item::create($data);
        $this->_set_new_mouvement($request , $item->id );
        return ['success' => true, 'message' => "Sauvegarder avec succès"   ,"data" => $this->_make_row_inventory($item)];
    }
    public function  detail_after_scanned_qrcode (Request $request){
        $item =  $item = Item::with((["article.category","mouvements"]))->find($request->route("item_id"));
        if (!$item || $item->deleted) {
            abort(404);
        }
        return view ("stock.article.scanned-detail",["item" => $item]);
    }

    /** Category item */
    public function category_data_list() {
        $data = [];
        $cats = ItemCategory::whereDeleted(0)->latest()->get();
        foreach ($cats as $cat) {
            $data[] = $this->_make_row_category( $cat);
        }
        return ["data"  => $data];
    }

    public function _make_row_category(ItemCategory $category)
    {
        $actions = modal_anchor(url("/stock/category/modal-form"), '<i class="fas fa-edit me-4 fs-3 text-info"></i>', ["title" => "Edition du " . $category->name , "data-post-cat_id" => $category->id]);
        $actions .= " " . js_anchor('<i class="fas fa-trash me-4 fs-3 text-danger"></i>', [ 'data-action-url' => url("/stock/category/delete"), "title" => "Supprimer","data-post-cat_id" => $category->id , "data-action" => "delete"]);
        return [
            "DT_RowId" => row_id("itemCategory", $category->id),
            'name' => $category->name,
            'code' => $category->code,
            'actions' =>  $actions,
        ];
    }
    public function category_modal_form(Request $request) {
        $data = [];
        $itemCategory = ItemCategory::find($request->cat_id) ?? new ItemCategory;
        $data['itemCategory'] =  $itemCategory;
        return view('stock.category.categorie-modal-form', $data);
    }
    public function category_save(CreateItemCategoryResquet $request) {
        try {
            $categorie = ItemCategory::updateOrCreate(["id" => $request->id], $request->except("_token"));
            return ["success" => true ,"message" => "Catégorie bien sauvegardée" ,"row_id" => $request->id ? row_id("itemCategory", $categorie->id) : null, "data" => $this->_make_row_category( $categorie)];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function category_delete(Request $request)
    {
        $ItemCategory = ItemCategory::find($request->cat_id);
        if ($request->input("cancel")) {
            $ItemCategory->update(["deleted" => 0]);
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row_category($ItemCategory)];
        } else {
            $ItemCategory->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
    /** End category item */
    /** Artilce gestion */
    public function article_data_list() {
        $data = [];
        $articles = ItemType::with(["category"])->whereDeleted(0)->latest()->get();
        foreach ($articles as $article) {
            $data[] = $this->_make_row_article( $article);
        }
        return ["data"  => $data];
    }
    
    public function _make_row_article(ItemType $article)
    {
        $actions = modal_anchor(url("/stock/article/modal-form"), '<i class="fas fa-edit me-4 fs-3 text-info"></i>', ["title" => "Edition du " . $article->name , "data-post-article_id" => $article->id]);
        $actions .= " " . js_anchor('<i class="fas fa-trash me-4 fs-3 text-danger"></i>', [ 'data-action-url' => url("/stock/article/delete"), "title" => "Supprimer","data-post-article_id" => $article->id , "data-action" => "delete"]);
        return [
            "DT_RowId" => row_id("article", $article->id),
            'name' => $article->name,
            'code' => $article->code ?? "-",
            'category' =>  $article->category->name  ??  "-",
            'sub_category' =>   $article->sub_category ??  "-",
            'actions' =>  $actions,
        ];
    }
    public function article_modal_form(Request $request) {
        $data = [];
        $article = ItemType::find($request->article_id) ?? new ItemType;
        $data['article'] =  $article;
        $data['categories'] =  ItemCategory::whereDeleted(0)->latest()->get();
        $data['sub_cats'] =  ItemType::getSubCategory();
        return view('stock.article.article-modal-form', $data);
    }
    public function article_save(ItemTypeRequest $request) {
        try {
            $data = $request->except("_token");
            $data["category_id"] = $request->category_id == "non-definie" ? null :  $request->category_id;
            $data["sub_category"] = $request->sub_category == "non-definie" ? null :  $request->sub_category;
            $article = ItemType::updateOrCreate(["id" => $request->id], $data);
            return ["success" => true ,"message" => "Article bien sauvegardée" ,"row_id" => $request->id ? row_id("article", $article->id) : null, "data" => $this->_make_row_article( $article)];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function article_delete(Request $request)
    {
        $article = ItemType::find($request->article_id);
        if ($request->input("cancel")) {
            $article->update(["deleted" => 0]);
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row_article($article)];
        } else {
            $article->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }

    /** Location */
    public function location_data_list() {
        $data = [];
        $location = Location::whereDeleted(0)->latest()->get();
        foreach ($location as $location) {
            $data[] = $this->_make_row_location( $location);
        }
        return ["data"  => $data];
    }

    public function _make_row_location(Location $location)
    {
        if (($location->name == "Stock" ||  $location->id == Location::STOCK_ID)) {
            $actions = "";
        }else{
            $actions = modal_anchor(url("/stock/location/modal-form"), '<i class="fas fa-edit me-4 fs-3 text-info"></i>', ["title" => "Edition du " . $location->name , "data-post-location_id" => $location->id]);
            $actions .= " " . js_anchor('<i class="fas fa-trash me-4 fs-3 text-danger"></i>', [ 'data-action-url' => url("/stock/location/delete"), "title" => "Supprimer","data-post-location_id" => $location->id , "data-action" => "delete"]);
        }
        return [
            "DT_RowId" => row_id("location", $location->id),
            'name' => $location->name,
            'code' => $location->code_location ?? "",
            'actions' =>  $actions,
        ];
    }
    public function location_modal_form(Request $request) {
        $data = [];
        $location = Location::find($request->location_id) ?? new location;
        $data['location'] =  $location;
        return view('stock.location.modal-form', $data);
    }
    public function location_save( Request $request) {
        try {
            $data = $request->except("_token");
            $location = Location::updateOrCreate(["id" => $request->id], $data);
            return ["success" => true ,"message" => "Emplacement bien sauvegardée" ,"row_id" => $request->id ? row_id("location", $location->id) : null, "data" => $this->_make_row_location( $location)];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function location_delete(Request $request)
    {
        $location = Location::find($request->location_id);
        if ($request->input("cancel")) {
            $location->update(["deleted" => 0]);
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row_location($location)];
        } else {
            $location->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
    public function get_location_code(Request $request)
    {
        try {
            $location = Location::find($request->location_id);
            return ["success" => true,  "code" => $location->code_location ?? $location->name  ];
        } catch (Exception $e) {
            return ["success" => true,  "message" => $e->getMessage() ];
        }
    }
    public function item_location_history(Request $request)
    {
        $data = [];
        $mouvements = ItemMovement::with(["location"])->where("item_id" , $request->item_id )->oldest()->get();
        $count_mouvmnt = $mouvements->count();
        if (! $count_mouvmnt ) {
            return ["data" => [
                [
                    "location" =>  Location::find(Location::STOCK_ID)->name . " <span class='badge badge-info'>Emplacement actuel</span>",
                    "date" => now()->translatedFormat("d-M-Y"),
                    "used_by" => "",
                ]
            ]];
        }
        $i = 1;
        foreach ($mouvements  as $mouvement) {
           $location =  $mouvement->place . $mouvement->location->code_location . "({$mouvement->location->name})";
           if ( $i == $count_mouvmnt) {
                $location .= "<span class='badge badge-info'>Emplacement actuel</span>";
           } 
           $data[] = [
            "location" =>  $location,
            "date" => $mouvement->created_at->translatedFormat("d-M-Y"),
            "used_by" => User::findMany(explode(",",$mouvement->user_id))->implode("sortname", ", "),
           ];
           $i++;
        }
        return ["data" => $data];
    }
}