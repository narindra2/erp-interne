<x-base-layout>
    <div class="row">
        <div class="col-xxl-12 mb-10" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> Gestion Achat </span>
                        <div class="text-muted fs-7 fw-bold"></div>
                    </div>
                    <div class="card-toolbar">
                        @php
                            echo modal_anchor(url('/purchases/demande-form'), '<i class="fas fa-plus"></i> Creer une demande', ['title' => "Creer une demande d'achat", 'class' => 'btn btn-sm btn-light-info' , "data-modal-lg" => true]);
                        @endphp
                        {{-- <a href="{{ url('/purchases/new') }}" class="btn btn-sm btn-light-info"><i class="fas fa-plus-circle"></i> Nouvel Achat</a> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-12" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-body py-5">
                    <div class="d-flex justify-content-end mb-5">
                        <div class="me-4 my-2 ml-3">
                            <div class="d-flex align-items-center position-relative my-1">
                                <input type="text" id="search-purchases" autocomplete="off"
                                    class="form-control form-control-solid form-select-sm w-200px ps-9 "
                                    placeholder="{{ trans('lang.search') }}">
                            </div>
                        </div>
                        <div class="me-4 my-2">
                            <a id="do-reload" title = "Recharger" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                                <i class="fas fa-sync-alt" style="width: 10px;"></i>
                            </a>
                        </div>
                    </div>
                    <table id="purchases" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.purchasesList = $("#purchases").DataTable({
                    processing: true,
                    dom : "tpr",
                    columns: [ 
                        {data :"info" , title: ''},
                        {data :"date" , title: 'Date'},
                        {data :"author" , title: 'Createur/Demandeur'},
                        {data :"items" , title: 'Article'},
                        {data :"total_price" , title: 'Total'},
                        {data :"method" , title: 'Méthode de paiement'},
                        {data: "files", title: "Fichiers joints"},
                        {data: "status", title: "Statut"},
                        {data :"created_at" , title: 'Crée le'},
                        {data :"actions"}
                    ],
                    ajax: {
                        url: url("/purchases/data_list"),
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                });
                $('#search-purchases').on('keyup', function() {
                    dataTableInstance.purchasesList.search(this.value).draw();
                });
                $('#do-reload').on('click', function(e) {
                    dataTableInstance.purchasesList.ajax.reload();
                });
            });
        </script>
    @endsection
</x-base-layout>
