<x-base-layout>
    <div class="card shadow-sm  mb-3 ">
        <div class="card-header border-1 pt-1">
            <div class="me-2 card-title align-items-start ">
                <span class="card-label  fs-3 mb-1"> @lang('lang.tickets-lists') </span>
                <div class="text-muted fs-7 fw-bold"></div>
            </div>
            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                data-bs-original-title="{{ trans('lang.add-new-ticket') }}">
                @php
                    echo modal_anchor(url('/ticket/modal-form'), '<i class="fas fa-plus"></i>' . trans('lang.add-new-ticket'), ['title' => trans('lang.add-new-ticket'), 'class' => 'btn btn-sm btn-light-primary']);
                @endphp
            </div>
        </div>
    </div>
    <div class="card shadow-sm  ">
        <div class="card-body py-5">
            <div class="d-flex justify-content-end mb-5">
                <div class="filter-datatable">
                    @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "ticketsTable"])
                </div>
                &nbsp; &nbsp;
                <div class="me-4 my-2 ml-3">
                    <div class="d-flex align-items-center position-relative my-1">
                        <input type="text" id="search_tickets" autocomplete="off"
                            class="form-control form-control-solid form-select-sm w-200px ps-9 "
                            placeholder="{{ trans('lang.search') }}">
                    </div>
                </div>
                <div class="me-4 my-2">
                    <a id="do-search-project" title = "Recharger" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                        <i class="fas fa-sync-alt" style="width: 10px;"></i>
                    </a>
                </div>
            </div>
            <table id="ticketsTable" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
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
                dataTableInstance.ticketsTable = $("#ticketsTable").DataTable({
                    processing: true,
                    dom : "ltpr",
                    ordering :false,
                    columns:[
                        {data :"resolue" , title: '', "class":"text-left"},
                        {data :"id" , title: 'N°', "class":"text-left"},
                        {data :"owner" , title: 'Ticket de', "class":"text-rigth w-100px"},
                        {data :"autor" , title: 'Auteur', "class":"text-left w-50px"},
                        {data :"urgence" , title: 'Priorité', "class":"text-left"},
                        {data :"description" , title: 'Déscription', "class":"text-left"},
                        {data :"status" , title: 'Status', "class":"text-left"},
                        {data :"assign_to" , title: 'Assigné à', "class":"text-left"},
                        {data :"resolve_by" , title: 'Resolue par', "class":"text-left"},
                        {data :"created_at" , title: 'Creer le', "class":"text-rigth w-100px"},
                        {data :"resolve_date" , title: 'Resolue le', "class":"text-left"},
                    ],  
                    ajax: {
                        url: url("/ticket/list"),
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
                $('#search_tickets').on('keyup', function() {
                    dataTableInstance.ticketsTable.search(this.value).draw();
                });
                $('.ticketsTable').on('change', function() {
                    dataTableInstance.ticketsTable.ajax.reload();
                });
                $('#do-search-project').on('click', function(e) {
                    dataTableInstance.ticketsTable.ajax.reload();
                });
                $(document).on('click',".resolve-ticket-input", function(e) {
                   var ticket_id = $(this).attr("data-ticket-id");
                   var button = document.querySelector("#button-resolve-ticket-"+ ticket_id);
                   button.setAttribute("data-kt-indicator", "on");
                    $.ajax({
                        url: url("/ticket/set/resolve"),
                        data: {
                            "_token" : _token,
                            "ticket_id" : ticket_id
                        },
                        type: 'POST',
                        success: function (response) {
                            button.removeAttribute("data-kt-indicator");
                            dataTableUpdateRow(dataTableInstance.ticketsTable, response.row_id, response.data)
                            toastr.success(response.message)
                        },
                        error: function () {
                            button.removeAttribute("data-kt-indicator");
                            toastr.success("error")
                        }
                    });
                });
            })
        </script>
    @endsection
</x-base-layout>
