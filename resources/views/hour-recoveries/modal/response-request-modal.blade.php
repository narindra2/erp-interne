<form class="form" id="response-hour-recovery" method="POST" action="{{ url("/response-hour-recoveries") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{$hourRecovery->id}}">
        
        <div class="row">
            <div class="col-md-6">
                <div class="alert  bg-light-success border border-success  d-flex flex-column flex-sm-row w-100 p-5 mb-2">
                    <label for="accept" class="radio radio-success">
                        <input type="radio" name="response" id="accept" value="1">
                        <span>Accepter</span>
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert  bg-light-danger border border-danger  d-flex flex-column flex-sm-row w-100 p-5 mb-2">
                    <label for="Refuser" class="radio radio-danger">
                        <input type="radio" name="response" id="Refuser" value="0">
                        <span>Refuser</span>
                    </label>
                </div>
            </div>
        </div>

   </div>
    <div class="card-footer">
        <div class="d-flex justify-content-end">
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
            <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
                @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" => trans('lang.sending')])
            </button>
        </div>
    </div> 
</form>
<script>
$(document).ready(function() {
    KTApp.initSelect2();
    KTApp.initBootstrapPopovers();

    $("#response-hour-recovery").appForm({
        onSuccess: function(response) {
            if (response.row_id) {
                console.log(response.data);
                dataTableUpdateRow(dataTableInstance.hourRecovery, response.row_id, response.data) 
            }
        },
    });




})
</script>
