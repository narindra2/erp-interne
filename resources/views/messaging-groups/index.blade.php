<x-base-layout>
    <div class="row">
        <div class="col-xxl-12 mb-10" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> Gestion des canaux </span>
                        <div class="text-muted fs-7 fw-bold"></div>
                    </div>
                    {{-- <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                        data-bs-original-title="{{ trans('lang.type_of') }}">
                        @php
                            echo modal_anchor(url("/public-holidays/modal"), '<i class="fas fa-plus"></i>' . trans('lang.new-public-holiday'), ['title' => trans('lang.new-public-holiday'), 'class' => 'btn btn-sm btn-light-primary']);
                        @endphp
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="col-xxl-12" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-body py-5">
                    <table id="messageGroup" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.messageGroup = $("#messageGroup").DataTable({
                    processing: true,
                    columns: [ 
                        {data :"name" , title: 'Nom'},
                        {data :"nb_participants", title: 'Nb participants'},
                    ],
                    ajax: {
                        url: url("/messaging/groups/data-list"),
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                });
            })
        </script>
    @endsection
</x-base-layout>
