<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemMovementLastAssignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //$request->request->add(['variable' => 'value']); //add request
        $users = $this->getUsersAssigned();
        return [
            "DT_RowId" => row_id("itemMovement", $this->id),
            'code' => $this->item->code,
            'item' => $this->item->type->name,
            'location' => $this->location->name,
            'local' => $this->location->getLocal(),
            'users' => view('item-movements.columns.users-assigned', ['users' => $users, 'id' => $this->id, 'showAddBtn' => true])->render(),
            'actions' => modal_anchor(url('/item-movements/item-historic/' . $this->item->id), '<i class="text-hover-primary fas fa-info-circle fs-3"></i>', ['title' => 'Voir les historiques', 'data-modal-xl' => true,'data-post-id' => 1])
        ];
    }
}
