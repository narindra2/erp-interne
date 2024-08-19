<div class="d-flex justify-content-around">
    {{-- <a href="#" class="btn btn-sm btn-light-primary font-weight-bold mr-2">Infos</a> --}}
    @php
        echo modal_anchor(url("/days-off/information/modal/$dayOff->id"), '<i class=" text-primary  fas fa-info-circle"></i>', ["title" => "Plus d'informations", "data-modal-lg" => true, "class"=> "text-primary "]);
    @endphp

    {{-- <a href="#" class="btn btn-sm btn-light-success font-weight-bold mr-2">Valider</a>
    <a href="#" class="btn btn-sm btn-light-danger font-weight-bold mr-2">Refuser</a> --}}
</div>