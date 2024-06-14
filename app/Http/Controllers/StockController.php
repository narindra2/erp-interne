<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Http\Requests\CreateItemCategoryResquet;

class StockController extends Controller
{
    public function index() {
        return view('stock.index');
    }
    public function inventory() {
        return view('stock.tabs.inventory');
    }


    /** Category item */
    public function category() {
        return view('stock.tabs.category');
    }
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
        $actions = modal_anchor(url("/stock/category/modal-form"), '<i class="fas fa-edit me-4 text-info"></i>', ["title" => "Edition du " . $category->name , "data-post-cat_id" => $category->id]);
        $actions .= " " . js_anchor('<i class="fas fa-trash me-4 text-danger"></i>', [ 'data-action-url' => url("/stock/category/delete"), "title" => "Supprimer","data-post-cat_id" => $category->id , "data-action" => "delete"]);
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
}
