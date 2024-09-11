<div class="card shadow-sm  ">
    <div class=" mx-2 ">
        <ul class="nav nav-tabs nav-line-tabs  fs-6">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#status-report">Rapport d 'etat</a>
            </li>
            @if (auth()->user()->isCp())
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#cumulative-hour">Heure cumulé</a>
                </li>
            @endif
        </ul>
    </div> 
</div>

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="status-report" role="tabpanel">
        <div class="card shadow-sm  ">
            <div class="card-body " >
                <div class="d-flex justify-content-end ">
                    <div class="  me-4 mt-6">
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
    </div>
    <div class="tab-pane fade " id="cumulative-hour" role="tabpanel">
        <div class="card shadow-sm  ">
            <div class="card-body " >
                <table id="cumulative-hour-table" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
            </div>
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
                    columnDefs: [ { targets: [0],visible: false} , ],
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
                @if (auth()->user()->isCp())
                dataTableInstance.cumulativeHour = $("#cumulative-hour-table").DataTable({
                    processing: true,
                    dom : "tr",
                    ordering :false,
                    pageLength: 100,
                    columnDefs: [ { targets: [0,],visible: false} , ],
                    columns:[
                        {data :"registration_number" , title: "Non d'employé", "class":"text-left"},
                        {data :"name" , title: '', "class":""},
                        {data :"minute_worked" , title: 'heure cumulé', "class":""},
                        {data :"last_update" , title: 'Dérnier mise à jour', "class":""},
                        
                    ],  
                    ajax: {
                        url: url("/status-report/cumulativeHour"),
                    },
                    language: {
                        url: url("/library/dataTable/datatable-fr.json")
                    },
                    
                })
                @endif
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
