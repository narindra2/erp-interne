<x-base-layout>
    <div class="row">
        <div class="col-xxl-12 mb-10" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> @lang('lang.complement hour') </span>
                        <div class="text-muted fs-7 fw-bold"></div>
                    </div>
                    <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                        data-bs-original-title="">
                        @php
                            echo modal_anchor(url("/complement-hours/modal-form"), '<i class="fas fa-plus"></i> Nouveau', ['title' => "Nouveau complément d'heure", 'class' => 'btn btn-sm btn-light-primary']);
                        @endphp
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-12" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-body py-5">
                    <table id="complementHour" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.complementHour = $("#complementHour").DataTable({
                    processing: true,
                    columns: [ 
                        {data :"day" , title: 'Date'},
                        {data :"registration_number" , title: 'Matricule'},
                        {data :"name" , title: "Nom"},
                        {data: "duration", title: 'Durée'},
                        {data: "type", title: 'Type'},
                        {data: "actions", orderable: false, searchable: false},
                    ],
                    ajax: {
                        url: url("/complement-hours/dataList"),
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                });

                
            })
        </script>
    @endsection
</x-base-layout>
