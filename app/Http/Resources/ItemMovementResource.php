<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemMovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $actions = "<div class='d-flex justify-content-around'>";
        $actions .= modal_anchor(url("/item-movements/modal-detail/". $this->id), '<i class="fas fa-info text-primary fs-3"></i>', ["title" => "Historique sur " . $this->name, "data-modal-xl" => true]);
        $actions .= modal_anchor(url("/items/formModal/". $this->id), '<i class="far fa-edit text-info fs-3"></i>', ["title" => "Editer " . $this->name]);
        $actions .= js_anchor('<i class="far fa-trash-alt text-danger fs-3"></i>', [ 'data-action-url' => url("/items-delete/" . $this->id), "title" => "Supprimer", "data-action" => "delete"]);
        $actions .= "</div>";
        return [
            'name' => $this->name,
            'reference' => $this->reference,
            'qr_code' => $this->qr_code,
            'quantity_available' => $this->quantity_available,
            'actions' => $actions
        ];
    }
}
