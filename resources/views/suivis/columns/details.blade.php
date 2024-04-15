@if (isset($item->id) && !$clone)
<div class="d-flex align-items-center mx-1">
    @php
        echo modal_anchor(url("/suivi/more-detail"),'<i class="fas fa-info-circle"></i>',["data-drawer" =>true ,"data-post-item_id" => $item->id ?? 0 ,"data-modal-title" => "<h3 class='card-title fs-3 fw-bold text-white flex-column m-0'>".$item->suivi->folder_name."<small class='text-white opacity-50 fs-7 fw-semibold pt-1'>Réf :  {$item->suivi->ref} </small></h3>", "title" => "Voir les details supplémentaire"  ])
    @endphp  
</div>
   
@endif
