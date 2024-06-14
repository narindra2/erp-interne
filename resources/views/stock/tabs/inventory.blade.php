<div class="card shadow-sm">
    <div class="d-flex justify-content-end mx-5 my-5">
        @php
            echo modal_anchor(url('/item-movements/new'), '<button class="btn btn-light-success font-weight-bold mr-2 btn-sm"><i class="fas fa-plus fs-3"></i> Nouveau</button>', ['title' => 'Nouvel mouvement', 'data-post-id' => 1])
        @endphp
    </div>
    <div class="card-body">
        <table id="invetoryListDataTable" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover ">
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        dataTableInstance.invetoryListDataTable = $("#invetoryListDataTable").DataTable({
            processing: true,
            // dom: "ltpr",
            columns: [{
                    data: "code",
                    title: 'Code'
                },
                {
                    data: "item",
                    title: 'Article'
                },
                {
                    data: "location",
                    title: "Lieu"
                },
                {
                    data: "local",
                    title: "Local"
                },
                {
                    data: "users",
                    title: "Utilisateur(s)"
                },
                {
                    data: "actions",
                    searchable: false,
                    orderable: false
                },
            ],
            ajax: {
                url: url("/item-movements/movements/data"),
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });
    });
</script>
