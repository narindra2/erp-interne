<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemCategoryRequest;
use App\Http\Requests\ItemTypeRequest;
use App\Http\Requests\ItemUnitRequest;
use App\Http\Resources\ItemCategoryResource;
use App\Http\Resources\ItemTypeResource;
use App\Http\Resources\UnitItemResource;
use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\UnitItem;
use Exception;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index() {
        return view('items.index');
    }

    public function geViewItemTypeList() {
        return view('items.tabs.itemTypeList');
    }

    public function getListOfTypeList() {
        $itemTypes = ItemType::with('unit')->whereDeleted(0)->get();
        return ItemTypeResource::collection($itemTypes);
    }

    public function itemTypeForm(ItemType $itemType) {
        $data = [];
        $data['itemType'] = $itemType;
        $data['itemCategories'] = ItemCategory::whereDeleted(0)->get();
        $data['units'] = UnitItem::whereDeleted(0)->get();
        return view('items.modal.itemTypeModal', $data);
    }

    public function saveItemType(ItemTypeRequest $request) {
        try {
            $itemType = ItemType::updateOrCreate(["id" => $request->id], $request->except("_token"));
            return ["success" => true ,"message" => "Article sauvegardé", "row_id" => $request->id ? row_id("itemType", $itemType->id) : null, "data" => new ItemTypeResource($itemType)];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteItemType(ItemType $itemType) {
        $itemType->deleted = true;
        $itemType->save();
        return ['success' => true, "message" => "Article supprimé"];
    }

    public function getViewItemCategorie() {
        return view('items.tabs.itemCategoryList');
    }

    public function getListOfItemCategorie() {
        $data = ItemCategory::whereDeleted(0)->get();
        return ItemCategoryResource::collection($data);
    }

    public function itemCategoryForm(ItemCategory $itemCategory) {
        $data = [];
        $data['itemCategory'] = $itemCategory;
        return view('items.modal.itemCategoryModal', $data);
    }

    public function saveItemCategory(ItemCategoryRequest $request) {
        try {
            $itemCategory = ItemCategory::updateOrCreate(["id" => $request->id], $request->except("_token"));
            return ["success" => true ,"message" => "Catégorie sauvegardée" ,"row_id" => $request->id ? row_id("itemCategory", $itemCategory->id) : null, "data" => new ItemCategoryResource($itemCategory)];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteItemCategory(ItemCategory $itemCategory) {
       

        
        return ['success' => true, "message" => "Catégorie supprimée"];
    }

    public function getViewItemUnit() {
        return view('items.tabs.itemUnit');
    }

    public function getListOfItemUnit() {
        $data = UnitItem::whereDeleted(0)->get();
        return UnitItemResource::collection($data);
    }

    public function itemUnitForm(UnitItem $unitItem) {
        $data = [];
        $data['unitItem'] = $unitItem;
        return view('items.modal.itemUnit', $data);
    }

    public function saveItemUnit(ItemUnitRequest $request) {
        try {
            $unitItem = UnitItem::updateOrCreate(["id" => $request->id], $request->except("_token"));
            return ["success" => true ,"message" => "Unité sauvegardée" ,"row_id" => $request->id ? row_id("unitItem", $unitItem->id) : null, "data" => new UnitItemResource($unitItem)];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteItemUnit(UnitItem $unitItem) {
        $unitItem->deleted = true;
        $unitItem->save();
        return ['success' => true, "message" => "Unité supprimée"];
    }
}
