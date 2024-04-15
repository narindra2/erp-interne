<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageGroupParticipantResource extends JsonResource
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
            "DT_RowId" => row_id("participants_id", $this->id),
            "photo" => view('messaging.groups.columns.photo', ['participant' => $this])->render(),
            'fullname' => $this->user->fullname,
            'actions' => view('messaging.groups.columns.actions', ['participant' => $this])->render()
        ];
    }
}
