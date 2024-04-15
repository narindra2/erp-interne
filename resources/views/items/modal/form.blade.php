<form class="form" id="item" method="POST" action="{{ url("/items") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{ $item->id }}">
        <div class="form-group row">
            <label class="col-3 col-form-label mb-4">Type</label>
            <div class="col-8">
                <select id="type_id" name="type_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="true"
                data-msg-required="@lang('lang.required')">
                    <option disabled selected >-- Type --</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}"
                            @if ($type->id == $item->type_id)
                                selected
                            @endif>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-3 col-form-label mb-4">Nom</label>
           <div class="col-8">
               <input type="text" class="form-control form-control-solid form-control-sm" name="name" id="name" value="{{ $item->name }}" data-rule-required="true"
               data-msg-required="@lang('lang.required')">
           </div>
        </div>
        <div class="form-group row">
            <label class="col-3 col-form-label mb-4">Code QR</label>
           <div class="col-8">
               <input type="text" class="form-control form-control-solid form-control-sm" name="qr_code" id="qr_code" value="{{ $item->qr_code }}">
           </div>
        </div>
        <div class="form-group row">
            <label class="col-3 col-form-label mb-4">Référence</label>
           <div class="col-8">
               <input type="text" class="form-control form-control-solid form-control-sm" name="reference" id="reference" value="{{ $item->reference }}" >
           </div>
        </div>
        <div class="form-group row">
            <label class="col-3 col-form-label mb-4">Unité</label>
            <div class="col-8">
                <input type="text" class="form-control form-control-solid form-control-sm" name="unit" id="unit" value="{{ $item->unit }}" >
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

    $("#item").appForm({
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
