<?php

namespace App\Http\Resources;

use App\Models\Purchase;
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
        $detail = modal_anchor(url('/purchases/demande-form'), '<i class="fas fa-external-link-alt"></i> Detail', ['title' => "Detail demande d'achat", 'class' => 'btn btn-link btn-color-info' , "data-modal-lg" => true , "data-post-purchase_id" =>$this->id]);
        $itemsName = $this->details->pluck("itemType")->implode("name",", ");
        $sortItemsName=str_limite($itemsName ,15);
        $statusInfo = Purchase::getPurchaseStatusInfo($this->status);
        $statusText = get_array_value($statusInfo,"text");
        $statusColor = get_array_value($statusInfo,"color");
        $statusColor = get_array_value($statusInfo,"color");
        if ($this->status == Purchase::PURCHASED_PURCHASE) {
            // $detail.=  modal_anchor(url('/purchases/demande-form'), '<i class="fas fa-external-link-alt"></i> Detail', ['title' => "Stock", 'class' => 'btn btn-link btn-color-dark' , "data-modal-lg" => true , "data-post-purchase_id" =>$this->id]);
        }
        return [
            'info' => '<span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-30px  bg-info"></span>',
            'date' => $this->purchase_date->format("d-M-Y"),
            'author' => $this->author->sortname,
            // 'method' => "<span class='badge badge-sm badge-info'></span>" ,
            'method' => "<span class='badge badge-sm badge-light-info'>$this->method</span>" ,
            'items' => "<p title='$itemsName'> $sortItemsName</p>" ,
            'total_price' => "<h5>$this->total_price</h5>",
            'files' => $this->createColumnFiles(),
            'status' =>"<span class='badge badge-sm badge-$statusColor'>$statusText</span>" ,
            'created_at' => convert_to_real_time_humains($this->created_at),
            'actions' => $detail
        ];
    }

    public function createColumnFiles() {
        if ($this->files->count()) {
            return view('purchases.columns.files', ['files' => $this->files])->render();
        }
        return "";
    }
}
