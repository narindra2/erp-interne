<form class="form" id="assign-to-form" method="POST" action="{{ url("/add/ticket/assign") }}">
    @csrf
    <div class="card-body ">
        <div class="mb-1">
            <input class="form-control form-control form-control-solid my-5" id="search_assign" autocomplete="off" data-kt-autosize="true" placeholder="{{ trans('lang.search') }}" rows="1">
        </div>
        <div class="mb-2">
            <div class="mh-300px scroll-y me-n7 pe-7">
                <div class="table-responsive">
                    <table id="assignTable" class="table table-row-dashed align-middle table-hover"></table>
                </div>
            </div>
        </div>
        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
    </div>
    <div class="separator mx-1 my-8"></div>
        <div class="d-flex justify-content-end" >
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.close')</button>
            <button type="submit" id="add-member"   class=" btn btn-sm btn-light-primary  mr-2">
                @include('partials.general._button-indicator', ['label' => trans('lang.save') , "message" => trans("lang.sending")])
            </button>
        </div>
</form>
<script>
    $(document).ready(function() {
        dataTableInstance.assignTable = $("#assignTable").DataTable({
                    dom : 'tr',
                    processing: true,
                    ordering: false,
                    columns: [
                        {data: 'not_assigned',"class":"text-left","orderable":false,"searchable":true},
                        {data: 'not_assigned_input',"class":"text-left","orderable":false,"searchable":true},
                    ],
                    ajax: {
                        url: url("/load/user/it_not_assgned/{{$ticket->id }}"),
                    },
        });
        $('#search_assign').on('keyup', function() {
            dataTableInstance.assignTable.search(this.value).draw();
        });

        $("#assign-to-form").appForm({
            onSuccess: function(response) {
                if (response.data) {
                    dataTableUpdateRow(dataTableInstance.ticketsTable, response.row_id, response.data)
                }
            },
        })
    } );
</script>
