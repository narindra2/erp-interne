<div class="card shadow-sm">
    <div class="d-flex justify-content-end mx-5 my-5">
        @php
            echo modal_anchor(url('/items/modal-type/'), '<button class="btn btn-light-success font-weight-bold mr-2 btn-sm"><i class="fas fa-plus fs-3"></i> Nouveau</button>', ['title' => 'Nouvel article', 'data-post-id' => 1])
        @endphp
    </div>
    <div class="card-body">
        <table id="itemTypes" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover ">
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        dataTableInstance.itemTypes = $("#itemTypes").DataTable({
            processing: true,
            dom: "ltpr",
            columns: [{
                    data: "name",
                    title: 'Nom'
                },
                {
                    data: "brand",
                    title: 'Marque'
                },
                {
                    data: "unit",
                    title: 'Unité'
                },
                {
                    data: "unit_price",
                    title: "Prix"
                },
                {
                    data: "actions",
                    searchable: false,
                    orderable: false
                },
            ],
            ajax: {
                url: url("/items/list/data"),
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });
    });
</script>
