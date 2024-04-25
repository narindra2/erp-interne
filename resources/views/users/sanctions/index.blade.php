<x-base-layout>
    <div class="card shadow-sm  mb-3 ">
        <div class="card-header ">
            <div class=" card-title align-items-start ">
                <span class="card-label  fs-3 "> @lang('lang.sanctions') </span>
                <div class="text-muted fs-7 fw-bold"></div>
            </div>
            {{-- <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                data-bs-original-title="{{ trans('lang.add-new-ticket') }}">
                @php
                    echo modal_anchor(url('/ticket/modal-form'), '<i class="fas fa-plus"></i>' . trans('lang.add-new-ticket'), ['title' => trans('lang.add-new-ticket'), 'class' => 'btn btn-sm btn-light-primary']);
                @endphp
            </div> --}}
        </div>
    </div>
    <div class="card shadow-sm  ">
        <div class="card-body py-5">
            <div class="d-flex justify-content-end mb-5">
                <div class="filter-datatable">
                    @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "sactionsList"])
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
            <table id="sactionsList" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
        </div>
    </div>
    <style>
        .form-check.form-check-solid .form-check-input:checked {
        background-color: #50cd89;
    }
    </style>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.sactionsList = $("#sactionsList").DataTable({
                    processing: true,
                    dom : "tpr",
                    ordering :false,
                    columns:[
                        {data :"avatar" , title: '', "class":"text-left"},
                        {data :"user" , title: 'Employé', "class":"text-left"},
                        {data :"type" , title: 'Type', "class":"text-left w-50px"},
                        {data :"motif" , title: 'Motif', "class":"text-right "},
                        {data :"duration" , title: 'Durée en jrs', "class":"text-center"},
                        {data :"date" , title: 'Date', "class":"text-center"},
                    ],  
                    ajax: {
                        url: url("/users/sanctions/data_list"),
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
                $('#do-reload').on('click', function(e) {
                    dataTableInstance.sactionsList.ajax.reload();
                });
                $('#search-sanction').on('keyup', function() {
                    dataTableInstance.sactionsList.search(this.value).draw();
                });
                $('.sactionsList').on('change', function() {
                    dataTableInstance.sactionsList.ajax.reload();
                });
            })
        </script>
    @endsection
</x-base-layout>
