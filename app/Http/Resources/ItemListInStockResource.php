<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemListInStockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "DT_RowId" => row_id("item", $this->id),
            'type' => $this->type->name,
            'code' => $this->code,
            'created_at' => $this->created_at->translatedFormat("Y/m/d"),
            'location' => $this->lastMvt->location->name,
            'local' => $this->lastMvt->location->getLocal(),
            'actions' => $this->getActions()
        ];
    }

    public function getActions() {
        $actions = "<div class='d-flex justify-content-around'>";
        // $actions .= modal_anchor(url("/item-movements/modal-detail/". $this->id), '<i class="fas fa-info text-primary fs-3"></i>', ["title" => "Historique sur " . $this->name, "data-modal-xl" => true]);
        $actions .= modal_anchor(url("/item-movements/items/edit-code-form/". $this->id), '<i class="far fa-edit text-info fs-3"></i>', ["title" => "Editer " . $this->type->name]);
        // $actions .= js_anchor('<i class="far fa-trash-alt text-danger fs-3"></i>', [ 'data-action-url' => url("/items-delete/" . $this->id), "title" => "Supprimer", "data-action" => "delete"]);
        $actions .= "</div>";
        return $actions;
    }
}
