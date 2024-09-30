<x-base-layout>
    <div class="col-xxl-12 h-xl-100" id="category-section">
        <div class="card shadow-sm  mb-3 mb-xl-1">
            <div class="card-body py-5">
                <div class="d-flex justify-content-end mb-5">
                    <div class="filter-datatable">
                        @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "purchasesTable"])
                    </div>
                    &nbsp; &nbsp;
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
                    <div class="me-4 my-2 ml-3">
                        @php
                            echo modal_anchor(url('/prospect/prospect-modal-form'), '<i class="fas fa-plus"></i> Ajouter ', ['title' => "Ajouter", 'class' => 'btn btn-sm btn-light-info' , "data-modal-lg" => true]);
                        @endphp
                    </div>
                </div>
                <table id="purchasesTable" class="table table-row-dashed table-row-gray-200 align-middle  table-hover "></table>
            </div>
        </div>
    </div>
    <!--end::Col--> 
    @section('scripts')
    <script>
        $(document).ready(function() {
            dataTableInstance.purchasesTable = $("#purchasesTable").DataTable({
                processing: true,
                dom : "tpr",
                columns: [ 
                    {data :"info" , title: 'Nom de la société '},
                    {data :"date" , title: 'Telephone'},
                    {data :"author" , title: 'Email'},
                    {data :"author" , title: 'Prospect'},
                    {data :"num" , title: 'Dirigeant'},
                    {data :"num" , title: 'Site mere'},
                    {data :"created_at" , title: 'Crée le'},
                    {data: "status", title: "Statut"},
                    {data :"actions"},
                ],
                ajax: {
                    url: url("/purchases/data_list"),
                    data: function(data) {
                        <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                            data.{{ $input }} = $("#{{ $input }}").val();
                        <?php } ?>
                    }
                },
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                },
                initComplete: function(settings, json) {
                    KTApp.initBootstrapTooltips();
                }
            });
            
            $('#search-purchases').on('keyup', function() {
                dataTableInstance.purchasesTable.search(this.value).draw();
            });
            $('#do-reload').on('click', function(e) {
                dataTableInstance.purchasesTable.ajax.reload();
                setTimeout(() => {
                    KTApp.initBootstrapTooltips();
                }, 1000);
            });
            $('.purchasesTable').on('change', function() {
                dataTableInstance.purchasesTable.ajax.reload();
                setTimeout(() => {
                    KTApp.initBootstrapTooltips();
                }, 1000);
            });
        });
    </script>
@endsection
</x-base-layout>
