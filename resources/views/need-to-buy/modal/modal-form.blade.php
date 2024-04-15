<form class="form" id="needToBuy_form" method="POST" action="{{ url("/needToBuy/save") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{$needToBuy->id}}">
        <input type="hidden" name="author_id" value="{{$user->id}}">
        
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Article</label>
            <div class="col-6">
                <select id="item_type_id" name="item_type_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')">
                    <option disabled selected >-- Article --</option>
                    @foreach ($itemTypes as $itemType)
                        <option value="{{ $itemType->id }}"
                            @if ($itemType->id == $needToBuy->item_type_id)
                                selected
                            @endif>{{ $itemType->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Quantité</label>
            <div class="col-6">
                <input type="number" class="form-control form-control-sm form-control-solid" name="nb" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" value="{{ $needToBuy->nb }}">
            </div>
        </div>

        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Statut</label>
            <div class="col-6">
                <select id="status" name="status" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')">
                    <option disabled selected >-- Statut --</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}"
                            @if ($status == $needToBuy->status)
                                selected
                            @endif>{{ $status }}</option>
                        @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Departement</label>
            <div class="col-6">
                <select id="department_id" name="department_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')">
                    <option disabled selected >-- Departement --</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}"
                            @if ($department->id == $needToBuy->department_id)
                                selected
                            @endif>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Unité</label>
            <div class="col-6">
                <select id="unit_item_id" name="unit_item_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')">
                    <option disabled selected >-- Unité --</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}"
                            @if ($unit->id == $needToBuy->unit_item_id)
                                selected
                            @endif>{{ $unit->name }}</option>
                    @endforeach
                </select>
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

    $("#needToBuy_form").appForm({
        onSuccess: function(response) {
            if (response.row_id) {
                dataTableUpdateRow(dataTableInstance.needToBuy, response.row_id, response.data) 
            }else{
                dataTableaddRowIntheTop(dataTableInstance.needToBuy, response.data)
            }
        },
    });
})
</script>
