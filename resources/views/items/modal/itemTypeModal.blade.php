<form class="form" id="itemTypeForm" method="POST" action="{{ url("/items/save-type") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{ $itemType->id }}">
        
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Nom</label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="name" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" value="{{ $itemType->name }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Marque</label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="brand" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" value="{{ $itemType->brand }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Catégorie</label>
            <div class="col-6">
                <select id="item_category_id" name="item_category_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')">
                    <option disabled selected >-- Catégorie --</option>
                    @foreach ($itemCategories as $itemCategory)
                        <option value="{{ $itemCategory->id }}"
                            @if ($itemCategory->id == $itemType->item_category_id)
                                selected
                            @endif>{{ $itemCategory->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Unité</label>
            <div class="col-6">
                <select id="unit_id" name="unit_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')">
                    <option disabled selected >-- Unité --</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}"
                            @if ($unit->id == $itemType->unit°id)
                                selected
                            @endif>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Prix</label>
            <div class="col-6">
                <input type="number" min="0" class="form-control form-control-sm form-control-solid" name="unit_price" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" value="{{ $itemType->unit_price }}">
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

    $("#itemTypeForm").appForm({
        onSuccess: function(response) {
            if (response.row_id) {
                dataTableUpdateRow(dataTableInstance.itemTypes, response.row_id,response.data) 
            }else{
                dataTableaddRowIntheTop(dataTableInstance.itemTypes ,response.data)
            }
        },
    });
})
</script>
