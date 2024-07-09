<div class="card shadow-sm">
    <div class="d-flex justify-content-end mx-5 my-5">
        @php
            echo modal_anchor(url('/location/modal-form'), '<button class="btn btn-light-info font-weight-bold mr-2 btn-sm"><i class="fas fa-plus "></i> Ajouter une nouvelle catégorie</button>', ['title' => 'Nouvelle catégorie'])
        @endphp
    </div>
    <div class="card-body">
        <table id="location" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "> </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        dataTableInstance.location = $("#location").DataTable({
            processing: true,
            dom: "tpr",
            columns: [
                {data: "name",title: 'Nom' },
                {data: "code",title: 'Code place' },
                {data: "actions",searchable: false, orderable: false},
            ],
            ajax: {
                url: url("/stock/location/data-list"),
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });
    });
</script>
