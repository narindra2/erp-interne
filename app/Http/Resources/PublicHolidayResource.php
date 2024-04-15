<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicHolidayResource extends JsonResource
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
            "DT_RowId" => row_id("publicHoliday", $this->id),
            'name' => $this->name,
            'day' => $this->day,
            'duration' => $this->duration,
            'actions' => view('public-holidays.columns.actions', array("publicHoliday" => $this))->render()
        ];
    }
}
