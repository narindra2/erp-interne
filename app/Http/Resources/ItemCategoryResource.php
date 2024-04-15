<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemCategoryResource extends JsonResource
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
            "DT_RowId" => row_id("itemCategory", $this->id),
            'name' => $this->name,
            'actions' => $this->getActions()
        ];
    }

    public function getActions() {
        $actions = "<div class='d-flex justify-content-around'>";
        $actions .= modal_anchor(url("/items/modal-category/". $this->id), '<i class="fas fa-edit fs-3 text-primary"></i>', ["title" => "Editer " . $this->name]);
        $actions .= js_anchor('<i class="far fa-trash-alt text-danger fs-3"></i>', [ 'data-action-url' => url("/items/itemCategory/delete/" . $this->id), "title" => "Supprimer", "data-action" => "delete"]);
        $actions .= "</div>";
        return $actions;
    }
}
