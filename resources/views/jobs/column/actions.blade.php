<div class="d-flex justify-content-around">
    @php
        echo modal_anchor(url("/jobs/modal/$job->id"), '<i class="fas fa-edit"></i>', ["title" => "Editer", "data-modal-lg" => true, "class"=> "btn btn-light-success font-weight-bold mr-2"]);
    @endphp
</div>