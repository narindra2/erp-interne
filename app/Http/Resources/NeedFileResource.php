<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NeedFileResource extends JsonResource
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
            'name' => $this->getName(),
            'amount' => $this->amount,
            'created_at' => $this->created_at->translatedFormat('d M Y')
        ];
    }

    public function getName() {
        return view('need-to-buy.columns.link-download', ['needFile' => $this])->render();
    }
}
