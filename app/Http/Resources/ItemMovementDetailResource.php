<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemMovementDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $id = row_id("itemMovementDetail", $this->id);
        $actions = "<div class='d-flex justify-content-around'>";
        $actions .= "<i class='far fa-save updateItemMovement btn btn-sm btn-primary' data-row_id='$id' data-id='$this->id' title='Sauvegarder'></i>";
        $actions .= js_anchor('<i class="far fa-trash-alt btn btn-sm btn-danger"></i>', [ 'data-action-url' => url("/item-movements/delete/" . $this->id), "title" => "Supprimer", "data-action" => "delete"]);
        $actions .= "</div>";
        return [
            "DT_RowId" => $id,
            'user' => $this->user->sortname,
            'date' => $this->created_at->format('Y-m-d'),
            'input_quantity' => $this->createInput('input_quantity', $this->input_quantity, "number", ["min" => 0, 'id' => $id . "input_quantity"]),
            'output_quantity' => $this->createInput('output_quantity', $this->output_quantity, "number", ["min" => 0, 'id' => $id . "output_quantity"]),
            'price' => $this->createInput('price', $this->price, "number", ["min" => 0, 'id' => $id . "price"]),
            'actions' => $actions
        ];
    }

    public function createInput($name, $value, $type, $attributes = []) {
        $input = "<input type='$type' class='form-control form-control-solid form-control-sm w-150px' name='$name' value='$value'";
        foreach($attributes as $key => $value) {
            $input.= "$key='$value'";
        }
        return $input . ">";
    }
}
