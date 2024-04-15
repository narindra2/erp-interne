<form action="{{ url('/users-pointing-excel/save') }}" id="modal-form" method="POST">
    @csrf

    <div class="form-group row mb-5">
        <label class="col-form-label col-4">Fichier Excel</label>
        <div class="col-6">
            <input class="form-control form-control-sm" type="file" name="file" id="formFile" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
        </div>
    </div>

    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
            @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" => trans('lang.sending')])
        </button>
    </div>

</form>

<script>
    $(document).ready(function() {
        $('#formFile').on('change', function() {    
            var fileName = $(this).val();
            $(this).val(fileName);
        })
        $("#modal-form").appForm({
            onSuccess: function(response) {
                //dataTableaddRowIntheTop( dataTableInstance.my_days_off ,response.data)
                //dataTableInstance.pointingList.ajax.reload();
            },
            onFail: function(request,status,error){
                $("#formFile").val("");
                return true
            },
            onError: function () {
                $("#formFile").val("");
                return true
            },

        });
    });
</script>