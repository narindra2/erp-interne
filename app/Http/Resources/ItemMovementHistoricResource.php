<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemMovementHistoricResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $users = $this->getUsersAssigned();
        return [
            "DT_RowId" => row_id("historic", $this->id),
            "date" => $this->created_at->format("Y-m-d"),
            'location' => $this->location->name,
            'local' => $this->location->getLocal(),
            'users' => view('item-movements.columns.users-assigned', ['users' => $users, 'id' => $this->id, 'showAddBtn' => false])->render()
        ];
    }
}
