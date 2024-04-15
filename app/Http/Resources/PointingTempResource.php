<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PointingTempResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $value = $this->getTimeWorked();
        $userID = $this->user->id;
        return [
            'registration_number' => $this->user->registration_number,
            'name' => $this->user->fullname,
            'input' => "<input type='text' name='minute_worked$userID' id='$userID' class='form-control form-control-sm form-control-solid w-100px' value='$value' placeholder='HH:MM'>",
            'actions' => "<button class='btn btn-sm btn-light-success font-weight-bold mr-2 save-pointing' data-user_id='$userID'>Enregistrer</button>"
        ];
    }

    public function getTimeWorked() {
        if ($this->user->pointingTemp)  return $this->user->pointingTemp->minute_worked;
        return null;
    }
}
