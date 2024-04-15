<form class="form" id="item-movement-form" method="POST" action="{{ url("/item-movements/modal-save") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{ $itemMovement->id }}">
        <div class="form-group row">
            <label class="col-3 col-form-label mb-4">Article</label>
            <div class="col-8">
                <select id="item_id" name="item_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="true"
                data-msg-required="@lang('lang.required')">
                    <option disabled selected >-- Article --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}"
                            @if ($item->id == $itemMovement->id)
                                selected
                            @endif>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-3 col-form-label">Type de mouvement</label>
            <div class="col-9 col-form-label">
                <div class="radio-inline">
                    <label class="radio radio-outline radio-success mx-3">
                        <input type="radio" name="type" value="1"/>
                        <span></span>
                            Achat
                    </label>
                    <label class="radio radio-outline radio-success">
                        <input type="radio" name="type" value="2"/>
                        <span></span>
                            Consommation
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-3 col-form-label mb-4">Quantité</label>
           <div class="col-5">
               <input type="number" class="form-control form-control-solid form-control-sm" step="any" name="quantity" id="quantity" data-rule-required="true"
               data-msg-required="@lang('lang.required')">
           </div>
        </div>
        <div class="form-group row">
            <label class="col-3 col-form-label mb-4">Prix Total</label>
           <div class="col-5">
               <input type="number" class="form-control form-control-solid form-control-sm" step="any" name="price" id="price" data-rule-required="true"
               data-msg-required="@lang('lang.required')">
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

    $("#item-movement-form").appForm({
        onSuccess: function(response) {
            dataTableInstance.itemMovements.ajax.reload();
            // if (response.row_id) {
            //     dataTableUpdateRow(dataTableInstance.itemMovements, response.row_id, response.data) 
            // }else{
            //     dataTableaddRowIntheTop(dataTableInstance.itemMovements, response.data)
            // }
        },
    });
})
</script>
