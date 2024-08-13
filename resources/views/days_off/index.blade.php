<x-base-layout>
    <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-5 " style="height: 62px !important;background-size: auto calc(100% + 10rem); background-position-x: 100%">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="d-flex align-items-center">
                <!--begin::Icon-->
                <div class="symbol symbol-circle me-5">
                    <div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs020.svg-->
                        <span class="svg-icon svg-icon-2x svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M17.302 11.35L12.002 20.55H21.202C21.802 20.55 22.202 19.85 21.902 19.35L17.302 11.35Z" fill="black"></path>
                                <path opacity="0.3" d="M12.002 20.55H2.802C2.202 20.55 1.80202 19.85 2.10202 19.35L6.70203 11.45L12.002 20.55ZM11.302 3.45L6.70203 11.35H17.302L12.702 3.45C12.402 2.85 11.602 2.85 11.302 3.45Z" fill="black"></path>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                </div>
          
                <!--end::Icon-->
                <!--begin::Title-->
                <div class="d-flex flex-column">
                    <h2 class="mb-1">Gestion de congé</h2>
                    {{-- <span class="text-muted fw-bold text-muted d-block fs-7">
                        @php
                            echo messaging("ulrich@gmail.com", ['class' => 'text-gray-600', 'data-mail' => true, 'mail' => "mail", 'title' => trans('lang.mail-to')]);
                        @endphp
                    </span> --}}
                </div>
              
                <!--end::Title-->
            </div>
         
        </div>
     
    </div>
    <div class="card shadow-sm  mb-3 ">
        <div class="w100p pt10 d-flex justify-content-between">
            <div id="gantt-chart" style="width: 100%;"></div>
        </div>
    </div>
    <div class="card card-flush shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Liste des demandes à valider</h3>
            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                
                @php
                    echo modal_anchor(url("/request_days_off/modal"), "+ Demander des jours de congés", ["title" => "Formulaire de la demande", "data-modal-lg" => true, "class"=> "btn btn-sm btn-light-primary", "data-post-id" => 10]);
                @endphp
            </div>
        </div>
        <div class="card-body py-5">
            <div class="d-flex justify-content-end mb-5">
                <div class="filter-datatable">
                    @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "dayOffRequested"])
                </div>
                &nbsp; &nbsp;
                <div class="me-4 my-2 ml-3">
                    <a id="do-search-dayOffRequested" title = "Actualiser la liste" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                        <i class="fas fa-sync-alt" style="width: 12px;"></i>
                    </a>
                </div> 
            </div>
            <div class="table-responsive">
                <div class="d-flex flex-row-reverse">
                    <div class="me-4 my-2 ml-3">
                        <div class="d-flex align-items-center position-relative my-1">
                            <input type="search" id="search_day_off" autocomplete="off"
                                class="form-control form-control-solid form-select-sm w-200px ps-9 "
                                placeholder="{{ trans('lang.search') }}">
                        </div>
                    </div>
                </div>
                <table class="table table-row-dashed table-row-gray-300 gy-4  wrap align-middle" id="dayOffRequested"></table>
            </div>
        </div>
    </div>
@section('dynamic_link')
<link rel="stylesheet" href="{{ asset('library/gantt-chart/gantt.css') }}" />
@endsection
@section('dynamic_script')
<script src= {{ asset('library/momentjs/moment.min.js') }} ></script>
<script src= {{ asset('library/gantt-chart/gantt.js') }} ></script>
<script type="text/javascript">
     function loadGantt() {
        $("#gantt-chart").ganttView({
            monthNames: ["Jan", "Feb", "Mars", "Avr", "Mai", "Juin", "Jul", "Aout", "Sept", "Oct", "Nov", "Dec"],
            dayText: "jour",
            daysText: "jours",
            cellHeight: 50,
            showWeekends : true,
            dataUrl : url("/days-off/dataListGantt"),
        });
    }
    loadGantt();
   </script>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            dataTableInstance.dayOffRequested = $("#dayOffRequested").DataTable({
                processing: true,
                order: [[0, "desc"]],
                dom: "Bt<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-4'l><'col-sm-8'p>>",
                buttons: [
                        { extend: 'excel',className: 'btn btn-sm btn-light' },
                        { extend: 'csv', className: 'btn btn-sm btn-light' },
                        { extend: 'pdf', className: 'btn btn-sm btn-light' },
                ],
                "searching": true,
                ajax: {
                    url: url("/days-off/dataList"),
                    data: function(data) {
                        <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                            data.{{ $input }} = $("#{{ $input }}").val();
                        <?php } ?>
                        }
                },
                responsive: true,
                order: [[7, 'asc']],
                columns: [
                    {data: "created_at", title: 'Date de demande'},
                    {data: "registration_number", title: 'Matricule'},
                    {data: "name", title: 'Nom' ,css :'max-width: 200px; width:200px;'},
                    {data: "job", title: "Poste"},
                    {data: "start_date", title: "Debut"},
                    {data: "return_date", title: "Retour"},
                    {data: "duration", title: "Durée"},
                    {data: "nature", title: "Nature"},
                    {data: "status", title: "Status"},
                    {data: "status_dayoff", title: "Etat de congé"},
                    {data: "action", orderable: false, searchable: false}
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                },
            });

            $(".dayOffRequested").on("change", function() {
                dataTableInstance.dayOffRequested.ajax.reload();
            });

            $('#search_day_off').on('keyup', function() {
                dataTableInstance.dayOffRequested.search(this.value).draw();
            });

            $('#do-search-dayOffRequested').on('click', function(e) {
                    dataTableInstance.dayOffRequested.ajax.reload(); 
                    loadGantt();
                });
            $("#btn-delete").on("click", function() {
                var inputs = $(".dayOffRequested");

                for(var i = 0; i < inputs.length; i++){
                    inputs[i].val("");
                }
            });
            setTimeout(() => {
                    KTApp.initBootstrapTooltips();
            }, 300);
            
        });
    </script>
@endsection
</x-base-layout>