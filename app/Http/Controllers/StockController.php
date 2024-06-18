<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\ItemType;
use App\Models\UnitItem;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Http\Requests\ItemTypeRequest;
use App\Http\Requests\CreateItemCategoryResquet;

class StockController extends Controller
{
    public function index() {
        return view('stock.index');
    }
    /** Tabs */
    public function inventory() {
        return view('stock.tabs.inventory');
    }
    public function article() {
        return view('stock.tabs.articles');
    }

    public function category() {
        return view('stock.tabs.category');
    }
    /** End tabs */

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
