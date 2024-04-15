<x-base-layout>
    <div class="row">
        <div class="col-xxl-12 mb-10" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> Gestion de stock </span>
                        <div class="text-muted fs-7 fw-bold"></div>
                    </div>
                    <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                        data-bs-original-title="Nouvel mouvement">
                        @php
                            echo modal_anchor(url("/items/formModal/"), '<i class="fas fa-plus"></i>' . "Nouvel Article", ['title' => "Nouvel article", 'class' => 'btn btn-sm btn-light-info mx-3']);
                        @endphp
                        @php
                            echo modal_anchor(url("/item-movements/modal-form/"), '<i class="fas fa-plus"></i>' . "Nouvel mouvement", ['title' => "Nouvel mouvement", 'class' => 'btn btn-sm btn-light-primary mx-3']);
                        @endphp
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-12" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-body py-5">
                    <table id="itemMovements" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.itemMovements = $("#itemMovements").DataTable({
                    processing: true,
                    dom : "ltpr",
                    columns: [ 
                        {data: "name" , title: 'Article'},
                        {data: "reference" , title: 'Code'},
                        {data: "qr_code", title: "Local"},
                        {data: "quantity_available", title: "Lieu"},
                        {data: "actions", searchable: false, orderable: false}
                    ],
                    ajax: {
                        url: url("/item-movements/data"),
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                });
            });
        </script>
    @endsection
</x-base-layout>
