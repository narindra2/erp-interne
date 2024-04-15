<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SanctionListResource extends JsonResource
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
            "DT_RowId" => row_id("sanction", $this->id),
            'date' => $this->date->format("d/m/Y"),
            'reason' => $this->reason,
            'duration' => $this->getDuration(),
            'type' => $this->getTypeWithCss(),
            'actions' => $this->getActions()
        ];
    }

    public function getActions() {
        $actions = "<div class='d-flex justify-content-around'>";
        $actions .= modal_anchor(url("/users/sanctions/form/". $this->id), '<i class="fas fa-edit fs-3 text-primary"></i>', ["title" => "Editer sanction", 'data-post-user_id' => $this->user_id]);
        $actions .= js_anchor('<i class="far fa-trash-alt text-danger fs-3"></i>', [ 'data-action-url' => url("/users/sanctions/delete/" . $this->id), "title" => "Supprimer", "data-action" => "delete"]);
        $actions .= "</div>";
        return $actions;
    }
}
