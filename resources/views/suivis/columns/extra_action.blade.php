

@if ( !$item->can_delete_row())
    
@else
    @php
        $delete_class =  !$clone ? "show-on-hover" : "show-it" ;
        echo modal_anchor(url("/suivi/delete/row/confiramtion-modal"), '<i class="fas fa-trash " style="font-size:12px" ></i>', ["class" => "btn btn-sm btn-clean show-on-hover $delete_class ", 'title' => trans('lang.delete'),  'data-post-item_id' =>  $clone ?  0 : $item->id]);
    @endphp
    <style>
    .show-on-hover{
        opacity: 0;
    }
    .show-on-hover:hover{
        opacity: 30;
    }
    .show-it{
        opacity: 30;
    }
   
    </style>
@endif