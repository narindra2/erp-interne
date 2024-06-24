<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Purchase;
use App\Models\UnitItem;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Http\Requests\ItemTypeRequest;
use App\Http\Requests\CreateItemCategoryResquet;
use App\Models\PurchaseNumInvoiceLine;

class StockController extends Controller
{
    public function index() {
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
    /** End tabs */

    /** Artilce inventory gestion */
    public function inventory_data_list(Request $request) {
        $data = [] ; $req = $request->all();
        $query = Item::query()->with(["article.category","purchase","num_invoice"])->whereDeleted(0);
        $etat = get_array_value( $req,"etat");
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
        $items = $query->latest()->get();
        foreach ($items as $item) {
            $data[] = $this->_make_row_inventory( $item);
        }
        return ["data"  => $data];
    }
    public function _make_row_inventory(Item $item) {
        $row["DT_RowId"] = row_id("invetory", $item->id);

        $row["code"] =  modal_anchor(url("/stock/inventory/modal-form"), "<span class='text-info fs-4'>1000/14565/MI/2024</span>", ["title" => "Edition du " . $item->code , "data-post-item_id" => $item->id]); ;
        $row["name"] = "<span class='text-dark fs-4 fw-bold'>{$item->article->name}</span>" ;
        $row["sub_cat"] = $item->article->sub_category;
        $row["cat"] = $item->article->category->name  ;
        $row["purchase"] = $item->purchase_id ?  $item->purchase->getNumPurchase() : "-";
        $etat_info = $item->getEtatInfo();
        $etat_class = $etat_info["color"];
        $etat_text = $etat_info["text"];
        $row["etat"] = "<span class='badge badge-$etat_class '> $etat_text </span>" ;
        $row["date"] = $item->date->format("d-M-Y");
        $row["prix_ht"] = $item->price_ht ? "<span class='badge badge-light-dark '>$item->price_ht Ar</span>"  :  "-" ;
        $row["prix_htt"] = $item->price_htt ? "<span class='badge badge-light-dark '>$item->price_htt Ar</span>"  :  "-" ;
        $row["num_invoice"] = $item->num_invoice_id ? $item->num_invoice->num_invoice : "-";
        $observation_sort  = str_limite($item->observation,20);
        $row["observation"] = "<span class='to-link' data-bs-toggle='tooltip'  data-bs-placement='top' title='{$item->observation}' > $observation_sort </span>";
        $row["detail"] =   modal_anchor(url("/stock/inventory/modal-form"), '<i class="fas fa-pen fs-4 me-3"></i>', ["title" => "Edition du " . $item->code , "data-post-item_id" => $item->id]);
        return $row;
    }
    public function inventor_modal_form(Request $request) {
        $item =  Item::with(["article.category"])->find($request->item_id);
        $purchases = Purchase::with(['author' ])->whereDeleted(0)->get();
        $num_invoices = PurchaseNumInvoiceLine::whereDeleted(0)->get();
        if ($request->item_id) {
            return view('stock.article.article-in-stock-modal-form', ["item" =>$item , "purchases"  =>$purchases , "num_invoices" => $num_invoices]);
        }else{
            return view('stock.article.article-in-stock-modal-form', ["purchases"  =>$purchases , "num_invoices" => $num_invoices]);
        }
    }

    public function save_inventor_from_update(Request $request) {
        $data = $request->all();
        $data["date"] = convert_date_to_database_date($request->date);
        $data["num_invoice_id"] = $request->num_invoice_id == "0" ? null  : $request->num_invoice_id;
        $data["purchase_id"] = $request->purchase_id == "0" ? null  : $request->purchase_id;
        $item =  Item::with(["article.category","purchase","num_invoice"])->find(  $request->item_id);
        $item->update($data);
        return ['success' => true, 'message' => "Mise à jour avec succès" , "row_id" =>  row_id("invetory",$item->id )  ,"data" => $this->_make_row_inventory( $item)];
    }

    public function create_article_migration_to_stock(Request $request)
    {
        $data = $request->all();
        $data["date"] = convert_date_to_database_date($request->date);
        $data["num_invoice_id"] = $request->num_invoice_id == "0" ? null  : $request->num_invoice_id;
        $data["etat"] = "fonctionnel";
        $item =  Item::updateOrCreate( ["id" => $request->item_id ], $data);
        return ['success' => true, 'message' => "Sauvegarder avec succès" , "item_id" => $item->id ];
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
}
