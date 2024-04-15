<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemMovementRequest;
use App\Http\Resources\ItemInStockResource;
use App\Http\Resources\ItemListInStockResource;
use App\Http\Resources\ItemMovementHistoricResource;
use App\Http\Resources\ItemMovementLastAssignResource;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\ItemStatus;
use App\Models\Location;
use Illuminate\Http\Request;

class ItemMovementController extends Controller
{
    //
    public function index() {
        return view('item-movements.index');
    }

    public function getLastAssignationData(Request $request) {
        $data = ItemMovement::getLastAssignationOfItems();
        return ItemMovementLastAssignResource::collection($data);
    }

    public function getViewAssign() {
        return view('item-movements.tabs.assign');
    }

    public function modalEditUser(Request $request, ItemMovement $itemMovement) {
        return view('item-movements.modals.edit-user', ['itemMovement' => $itemMovement, "userJobs" => get_users_cache()]);
    }

    public function saveUsers(Request $request) {
        $itemMovement = ItemMovement::saveAssignationOfUsers($request->except("_token"));
        return ['success' => true, "message" => 'Assignation faite avec succès', "row_id" => $request->id ? row_id("itemMovement", $itemMovement->id) : null, 'data' => new ItemMovementLastAssignResource($itemMovement)];
    }

    public function getModalHistoric(Item $item) {
        return view('item-movements.modals.historic', compact('item'));
    }

    public function getItemHistoric(Item $item) {
        $data = ItemMovement::getHistoricItem($item);
        return ItemMovementHistoricResource::collection($data);
    }

    public function modalNewItemMovement() {
        $data = [];
        $data['itemStatuses'] = ItemStatus::whereDeleted(0)->get();
        $data['items'] = Item::with(['type'])->whereDeleted(0)->get();
        $data['locations'] = Location::whereDeleted(0)->get();
        return view('item-movements.modals.new-mvt', $data);
    }

    public function saveNewMvt(ItemMovementRequest $request) {
        ItemMovement::create($request->input());
        return ["success" => true, "message" => "Mouvement sauvegardé avec succès"];
    }

    public function getViewStock() {
        $data = [];
        $data['basic_filter'] = ItemMovement::filterStock();
        return view("item-movements.tabs.stock", $data);
    }

    public function getDataStock(Request $request) {
        $data = ItemMovement::countItemsOnStock($request->input());
        return ItemInStockResource::collection($data);
    }

    public function getViewItem() {
        $data = [];
        $data['basic_filter'] = [];
        return view("item-movements.tabs.item", $data);
    }

    public function getDataItem(Request $request) {
        $data = Item::with(['type', 'lastMvt.location'])->whereDeleted(0)->get();
        return ItemListInStockResource::collection($data);
    }

    public function getEditCodeForm(Item $item) {
        return view('item-movements.modals.edit-item-code', ['item' => $item]);
    }

    public function saveNewItemCode(Request $request) {
        $item = Item::find($request->item_id);
        if ($request->code != "")
            $item->code = $request->code;
        $item->save();
        return ['success' => true];
    }
}
