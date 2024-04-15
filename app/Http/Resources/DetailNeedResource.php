<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailNeedResource extends JsonResource
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
            'DT_RowId' => row_id("detail_need", $this->id),
            'qty' => $this->qty,
            'status' => $this->statusHtml,
            'unit' => $this->unit ? $this->unit->name : '',
            'status_date' => $this->status_date->translatedFormat("Y-m-d"),
            'author' => $this->author->sortname
        ];
    }
}
