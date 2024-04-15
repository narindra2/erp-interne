<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $url = url("/purchases/details/" . $this->id);
        return [
            'date' => $this->purchase_date->format("Y-m-d"),
            'author' => $this->author->sortname,
            'method' => $this->method,
            'total_price' => $this->total_price,
            'files' => $this->createColumnFiles(),
            'actions' => "<a class='btn btn-light-success font-weight-bold mr-2 me-6 btn-sm' href='$url'>Détails</a>"
        ];
    }

    public function createColumnFiles() {
        if ($this->files->count()) {
            return view('purchases.columns.files', ['files' => $this->files])->render();
        }
        return "<span class='badge badge-light-primary fw-bolder fs-8 px-2 py-1 ms-2'>Aucun fichier</span>";
    }

    // @if ($dayOff->attachments->count())
    //                 <h6 class="my-5">Fichier joint</h6>
    //                 @foreach ($dayOff->attachments as $attachment) 
    //                     <span class="ml-2"><a href="{{ url('days-off/download-attachment') . "/" . $attachment->id }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset(theme()->getMediaUrlPath() . 'svg/files/upload.svg') }}" alt="" data-toggle="tooltip" data-placement="bottom" title="{{ $attachment->filename }}" height="40px" width="40px"></a></span>
    //                 @endforeach 
    //             @endif
}
