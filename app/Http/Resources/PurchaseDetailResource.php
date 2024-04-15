<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $totalPrice = $this->unit_price * $this->quantity;
        $actions = modal_anchor(url("/item-movements/modal-detail/". $this->id), '<i class="far fa-edit text-warning fs-3 mx-3"></i>', ["title" => "Editer"]);
        $actions .= modal_anchor(url("/item-movements/modal-detail/". $this->id), '<i class="fas fa-trash text-danger fs-3 mx-3"></i>', ["title" => "Editer"]);
        return [
            'item' => $this->itemType->name,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $totalPrice,
            'actions' => $actions
        ];
    }
}
