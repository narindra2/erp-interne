<x-base-layout>
    <div class="card shadow-sm  mb-3 ">
        <div class="card-header border-1 ">
            <h3 class="card-title">Rapport d'etat du : <span id="date-rapport" > {{ " " . now()->format("d/m/Y") }}</span></h3>
        </div>
    </div>
    <div class="card shadow-sm  ">
        <div class="card-body py-5">
            <div class="d-flex justify-content-end mb-5">
                <div class="filter-datatable">
                    @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "statusReport"])
                </div>
                &nbsp; &nbsp;
                <div class="me-4 my-2 ml-3">
                    <div class="d-flex align-items-center position-relative my-1">
                        <input type="text" id="search_statusReport" autocomplete="off"
                            class="form-control form-control-solid form-select-sm w-200px ps-9 "
                            placeholder="{{ trans('lang.search') }}">
                    </div>
                </div>
                <div class="me-4 my-2">
                    <a id="do-search-rapport" title ="Recharger" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                        <i class="fas fa-sync-alt" style="width: 10px;"></i>
                    </a>
                </div>
                <div class="me-4 my-2">
                    @php
                        echo modal_anchor(url('/status-report/modal-form'), '<i class="fas fa-plus"></i>' . "Ajouter un rapport d'etat", ['title' => "Ajouter un rapport d'etat", 'class' => 'btn btn-sm btn-light-primary',"data-modal-lg" => true]);
                    @endphp
                </div>
                
            </div>
            <table id="statusReport" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
        </div>
    </div>
    @section('dynamic_link')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endsection
    @section('dynamic_script')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @endsection
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.statusReport = $("#statusReport").DataTable({
                    processing: true,
                    dom : "tr",
                    ordering :false,
                    pageLength: 100,
                    columns:[
                        {data :"user" , title: "Non d'employé", "class":"text-left"},
                        {data :"date" , title: 'Date', "class":""},
                        {data :"rapport" , title: 'Rapport', "class":""},
                        {data :"status" , title: 'Etat', "class":"text-left"},
                        {data :"created_at" , title: 'Creer le', "class":""},
                        {data :"actions" , title: '', "class":""},
                        {data :"delete" , title: '', "class":""},
                    ],  
                    ajax: {
                        url: url("/status-report/dataList"),
                        data: function(data) {
                            <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                                data.{{ $input }} = $("#{{ $input }}").val();
                            <?php } ?>
                        }
                    },
                    language: {
                        url: url("/library/dataTable/datatable-fr.json")
                    },
                    
                }).on( 'draw', function () {
                    KTApp.initBootstrapPopovers();
                });
                $('#search_statusReport').on('keyup', function() {
                    dataTableInstance.statusReport.search(this.value).draw();
                });
                $('.statusReport').on('change', function() {
                    if($(this).attr("id") == "day_report"){
                        $("#date-rapport").text(" " + $(this).val())
                    }
                    dataTableInstance.statusReport.ajax.reload();
                });
                $('#do-search-rapport').on('click', function(e) {
                    dataTableInstance.statusReport.ajax.reload();
                });
                
            })
        </script>
    @endsection
</x-base-layout>
