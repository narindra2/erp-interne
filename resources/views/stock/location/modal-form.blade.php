<form class="form" id="locationForm" method="POST" action="{{ url("/stock/location/save") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{ $location->id }}">
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Lieu : </label>
            <div class="col-8">
                <input type="text" class="form-control form-control-sm form-control-solid" name="name" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')"  autocomplete="off" placeholder="Ex : Table 15" value="{{ $location->name }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Code d'empalcement : </label>
            <div class="col-8">
                <input type="text" class="form-control form-control-sm form-control-solid" name="code_location" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" autocomplete="off" placeholder="Code d'empalcement" value="{{ $location->code_location }}">
            </div>
        </div>
    </div>
<div class="card-footer">
    <div class="d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-info  mr-2">
            @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" => trans('lang.sending')])
        </button>
    </div>
</div>
</form>
<script>
$(document).ready(function() {
    KTApp.initSelect2();
    KTApp.initBootstrapPopovers();

    $("#locationForm").appForm({
        onSuccess: function(response) {
            if (response.row_id) {
                dataTableUpdateRow(dataTableInstance.location, response.row_id,response.data) 
            }else{
                dataTableaddRowIntheTop(dataTableInstance.location, response.data)
            }
        },
    });
})
</script>
