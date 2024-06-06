<x-base-layout>
    <div class="row">
        <div class="col-xxl-12 mb-10" >
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> @lang('lang.hour_recovery_management') </span>
                        <div class="text-muted fs-7 fw-bold"></div>
                    </div>
                    <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                        data-bs-original-title="{{ trans('lang.hour_recovery_management') }}">
                        @php
                            echo modal_anchor(url("/hour-recoveries/form"), '<i class="fas fa-plus"></i>' . trans('lang.new_hour_recovery'), ['title' => trans('lang.hour_recovery'), 'class' => 'btn btn-sm btn-light-primary', 'data-modal-lg' => true]);
                        @endphp
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-12" >
            
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="d-flex justify-content-end mb-5">
                    <div class="filter-datatable">
                        @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "hourRecovery"])
                    </div>
                    &nbsp; &nbsp;
                    <div class="me-4 my-2 ml-3">
                        <div class="d-flex align-items-center position-relative my-1">
                            <input type="text" id="search-sanction" autocomplete="off"
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
                <div class="card-body py-5">
                    <table id="hourRecovery" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.hourRecovery = $("#hourRecovery").DataTable({
                    processing: true,
                    dom : "tpr",
                    columns: [ 
                        {data :"date_of_absence" , title: "Date d'absence"},
                        {data :"fullname" , title: 'Nom'},
                        {data :"job" , title: 'Poste'},
                        {data :"recovery_date" , title: 'Date de récupération'},
                        {data :"duration_of_absence" , title: "Durée d'absence"},
                        {data :"hour_absence" , title: "Heure d'absence"},
                        {data: "description", title: 'Nature'},
                        {data: "is_validated", title: 'Etat'},
                        {data: "response", orderable: false, searchable: false},
                        {data: "action", orderable: false, searchable: false},
                        {data: "delete", orderable: false, searchable: false}
                    ],
                    ajax: {

                        url: url("/hour-recoveries-dataList"),
                        data: function(data) {
                            <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                                data.{{ $input }} = $("#{{ $input }}").val();
                            <?php } ?>
                        }
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                });
                $('#do-reload').on('click', function(e) {
                    dataTableInstance.hourRecovery.ajax.reload();
                });
                $('#search-sanction').on('keyup', function() {
                    dataTableInstance.hourRecovery.search(this.value).draw();
                });
                $('.hourRecovery').on('change', function() {
                    dataTableInstance.hourRecovery.ajax.reload();
                });
            })
        </script>
    @endsection
</x-base-layout>
