<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function PHPSTORM_META\map;

class PointingResumeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $hour = "";
        if ($this->time_worked < 0)     $hour = "<p class='text-danger'> - " . convertMinuteToHour(abs($this->time_worked)) ."</p>";
        else                            $hour = convertMinuteToHour($this->time_worked);

        return [
            'registration_number' => $this->registration_number,
            'name' => $this->name,
            'hour' => $hour,
            'action' => modal_anchor(url("/user-pointing/resume/details"), '<button type="button" class="btn btn-outline-success">Voir les détails</button>', ['class' => 'btn-sm h-30px btn-flex  btn-light-primary', 'data-modal-lg' => true, 'title' => 'Les détails de ' . $this->name, 'data-post-registration_number' => $this->registration_number])
        ];
    }
}
