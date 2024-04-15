<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NeedToBuyResource extends JsonResource
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
            "DT_RowId" => row_id("need", $this->id),
            'num_ticket' => $this->getNumTicket(),
            'name' => $this->itemType->name,
            'unit_price' => $this->unit_price,
            'quantity' => view('need-to-buy.columns.nb', ['nb' => $this->nb, 'needToBuy' => $this])->render(),
            'unit' => $this->unit ? $this->unit->name : '',
            'total_price' => $this->total_price,
            'author' => $this->author->sortname,
            'status' => $this->statusHtml,
            'department' => $this->getDepartmentName(),
            'actions' => $this->getActions()
        ];
    }

    public function getActions() {
        $actions = "<div class='d-flex justify-content-around'>";
        $actions .= modal_anchor(url('/needToBuy/infos/'. $this->id), '<i class="fas fa-info-circle fs-3 text-info"></i>', ['title' => "Infos Besoin", 'data-modal-lg' => true]);
        $actions .= modal_anchor(url('/needToBuy/form-modal/'. $this->id), '<i class="fas fa-edit fs-3 text-primary"></i>', ['title' => "Editer besoin"]);
        // $actions .= js_anchor('<i class="far fa-trash-alt text-danger fs-3"></i>', [ 'data-action-url' => url("/needToBuy/delete/" . $this->id), "title" => "Supprimer", "data-action" => "delete"]);
        // $actions .= '<i style="display:none;cursor:pointer" id="action-'. $this->id .'" class="fas fa-check fs-3 text-success validate-need" data-action-id=' . $this->id . '></i>';
        $actions .= "</div>";
        return $actions;
    }
}
