<x-base-layout>
    <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10"
        style="background-size: auto calc(100% + 10rem); background-position-x: 100%">
        <div class="card-header">
            <h3 class="card-title">Les demandes et absences</h3>
            <div class="card-toolbar">
                @php
                    echo modal_anchor(url('/request_days_off/modal'), '+ Demander  de congés', ['title' => 'Formulaire de la demande', 'data-modal-lg' => true, 'class' => 'btn btn-sm btn-light-primary', 'data-post-id' => 1]);
                @endphp
            </div>
        </div>
    </div>
    @if ($user->isCp() || $user->isM2p() )
        <div class="card shadow-sm  mb-3 ">
            <div class="w100p pt10">
                <div id="gantt-chart" style="width: 100%;"></div>
            </div>
        </div>
    @endif
    <div class="card card-flush shadow-sm">
        {{-- <div class="card-header">
            <h3 class="card-title">Mes jours d'absences</h3>
        </div> --}}
        <div class="card-body py-5">
            <div class="d-flex justify-content-end mb-5">
                <div class="filter-datatable">
                    @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "my_days_off"])
                </div>
                &nbsp; &nbsp;
                <div class="me-4 my-2 ml-3">
                    <div class="d-flex align-items-center position-relative my-1">
                        <input type="text" id="search_dayoff" autocomplete="off"
                            class="form-control form-control-solid form-select-sm w-200px ps-9 "
                            placeholder="{{ trans('lang.search') }} sur le resultat">
                    </div>
                </div>
                &nbsp; &nbsp;
                <div class="me-4 my-2">
                    <a id="do-reload" title = "Actualiser la table" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                        <i class="fas fa-sync-alt" style="width: 10px;"></i>
                    </a>
                </div>
            </div>
            <div class="">
                <table class=" table table-row-dashed table-row-gray-300 gy-4 align-middle" id="my_days_off"></table>
            </div>
        </div>
    </div>
@if ($user->isCp() || $user->isM2p())
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
                dataUrl : url("/days-off/dataListGantt"),
            });
        }
        loadGantt();
    </script>
    @endsection   
@endif

    @section('scripts')
        <script type="text/javascript">
            $(document).ready(function() {
                dataTableInstance.my_days_off = $("#my_days_off").DataTable({
                processing: true,
                order: [[0, "desc"]],
                dom: "B<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-4'l><'col-sm-8'p>>",
                buttons: [
                        { extend: 'excel',className: 'btn btn-sm btn-light' },
                        { extend: 'csv', className: 'btn btn-sm btn-light' },
                        { extend: 'pdf', className: 'btn btn-sm btn-light' },
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                },
                ajax: {
                    url: url("/my_days_off/dataList"),
                    data: function(data) {
                        <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                            data.{{ $input }} = $("#{{ $input }}").val();
                        <?php } ?>
                    }
                },
                columns: [
                    {data: "created_at", title: 'Date de demande'},
                    // {data: "matricule", title: 'matricule'},
                    {data: "applicant", title: 'congé de'},
                    {data: "author", title: 'Demandé par'},
                    {data: "start_date", title: 'Debut'},
                    {data: "return_date", title: 'Retour'},
                    {data: "duration", title: "Durée"},
                    // {data: "type", title: 'Type de demande'},
                    {data: "type", title: 'Type'},
                    {data: "nature", title: "Nature"},
                    // {data: "reason", title: "Description"},
                    {data: "status", title: "status"},
                    {data: "status_dayoff", title: "Etat"},
                    {data: "actions", class: "d-flex align-center"},
                ],
            });
                $('#search_dayoff').on('keyup', function() {
                    dataTableInstance.my_days_off.search(this.value).draw();
                });
                
                $(".my_days_off").on("change", function() {
                    dataTableInstance.my_days_off.ajax.reload();
                });
                $('#do-reload').on('click', function(e) {
                    dataTableInstance.my_days_off.ajax.reload();
                });
            });
        </script>
    @endsection
</x-base-layout>
