<div class="d-flex justify-content-around">
    @php
        echo modal_anchor(url("/public-holidays/modal/$publicHoliday->id"), '<i class="fas fa-edit"></i>', ["title" => "Editer", "data-modal-lg" => true, "class"=> "btn btn-light-success font-weight-bold mr-2"]);// echo modal_anchor(url("/public-holidays/modal/$publicHoliday->id"), '<i class="fas fa-trash"></i>', ["title" => "Supprimer", "data-modal-lg" => true, "class"=> "btn btn-light-danger font-weight-bold mr-2"]);
    @endphp
</div>