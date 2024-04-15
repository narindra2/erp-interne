<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ComplementHourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $actions = modal_anchor(url("/complement-hours/modal-form/" . $this->id), '<i class="far fa-edit text-primary fs-3"></i>', ["title" => "Mis à jour complément d'heure"]);
        $actions .= js_anchor('<i class="mx-3 far fa-trash-alt text-danger fs-3"></i>', [ 'data-action-url' => url("/complement-hours/delete/" . $this->id), "title" => "Supprimer", "data-action" => "delete"]);
        return [
            "DT_RowId" => row_id("complementHour", $this->id),
            "day" => $this->day->format("d M Y"),
            "registration_number" => $this->user->registration_number,
            "name" => $this->user->fullname,
            "duration" => $this->getTimeWorks(),
            "type" => $this->additionalHourType->name,
            "actions" => $actions
        ];
    }
}
