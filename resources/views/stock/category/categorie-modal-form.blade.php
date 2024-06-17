<form class="form" id="itemCategoryForm" method="POST" action="{{ url("/stock/category/save") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{ $itemCategory->id }}">
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Nom du catégorie : </label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="name" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')"  autocomplete="off" placeholder="Nom du categorie" value="{{ $itemCategory->name }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Code du catégorie : </label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="code" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" autocomplete="off" placeholder="Code du categorie" value="{{ $itemCategory->code }}">
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

    $("#itemCategoryForm").appForm({
        onSuccess: function(response) {
            if (response.row_id) {
                dataTableUpdateRow(dataTableInstance.itemCategory, response.row_id,response.data) 
            }else{
                dataTableaddRowIntheTop(dataTableInstance.itemCategory, response.data)
            }
        },
    });
})
</script>
