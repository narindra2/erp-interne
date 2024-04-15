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
                        <a href="{{ url('/purchases/new') }}" class="btn btn-sm btn-light-info"><i class="fas fa-plus-circle"></i> Nouvel Achat</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-12" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-body py-5">
                    <table id="purchases" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.jobs = $("#purchases").DataTable({
                    processing: true,
                    columns: [ 
                        {data :"date" , title: 'Date'},
                        {data :"author" , title: 'Responsable'},
                        {data :"method" , title: 'Méthode de paiement'},
                        {data: "files", title: "Fichiers joints"},
                        {data :"total_price" , title: 'Total'},
                        {data :"actions"}
                    ],
                    ajax: {
                        url: url("/purchases/data_list"),
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                });
            });
        </script>
    @endsection
</x-base-layout>
