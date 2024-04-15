<form class="form" id="job-form" method="POST" action="{{ url("/jobs") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{$job->id}}">
        
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Nom</label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="name" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" value="{{ $job->name }}">
            </div>
        </div>
    
    <div class="card-footer">
        <div class="d-flex justify-content-end">
            @if ($job->id != null)
                <button type="button" id="deleteJob" data-bs-dismiss="modal" aria-label="Close" data-id="{{ $job->id }}" class="btn btn-light-danger btn-sm mr-2 "> @lang('lang.delete')</button>
            @endif
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

    $("#job-form").appForm({
        onSuccess: function(response) {
            console.log(response);
            if (response.row_id) {
                dataTableUpdateRow(dataTableInstance.jobs, response.row_id,response.data) 
            }else{
                dataTableaddRowIntheTop(dataTableInstance.jobs ,response.data)
            }
        },
    });

    $("#deleteJob").on("click", function(e) {
        $.ajax({
            type: "POST",
            url: url("/job/delete/" + $(this).data("id")),
            data: {
                _token: _token
            },
            success: function (response) {
                $("#ajax-modal").modal().hide();
                dataTableInstance.jobs.ajax.reload();
            }
        });
    });

})
</script>
