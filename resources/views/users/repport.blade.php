    <div class="card shadow-sm  ">
        <div class="card-body " style="padding: 0rem 2.25rem;">
            <div class="d-flex justify-content-end ">
                <div class="mt-5 me-4">
                    <span >Rapport d'etat du <span >
                </div>
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
            </div>
            
            <table id="statusReport" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
        </div>
    </div>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            $(document).ready(function() {
                dataTableInstance.statusReport = $("#statusReport").DataTable({
                    processing: true,
                    dom : "tr",
                    ordering :false,
                    pageLength: 100,
                    columnDefs: [ { targets: [0,],visible: false} , ],
                    columns:[
                        {data :"user" , title: "Non d'employé", "class":"text-left"},
                        {data :"date" , title: 'Date', "class":""},
                        {data :"nature" , title: 'Rapport', "class": ""},
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
                            data.from_user_tab_view = true;
                        }
                    },
                    language: {
                        url: url("/library/dataTable/datatable-fr.json")
                    },
                    
                }).on( 'draw', function () {
                    KTApp.initBootstrapPopovers();
                });
                // $('#search_statusReport').on('keyup', function() {
                //     dataTableInstance.statusReport.search(this.value).draw();
                // });
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
