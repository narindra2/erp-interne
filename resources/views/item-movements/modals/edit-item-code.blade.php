<form action="{{ url("/item-movements/items/save-code-form") }}" method="POST" id="save-new-item-code">
    @csrf
    <input type="hidden" class="form-control form-control-solid" name="item_id" value="{{ $item->id }}">
    <div class="card">
        <div class="card-body">
            <div class="form-group row">
                <div class="form-group row mb-5">
                    <label class="col-form-label col-3">Code</label>
                    <div class="col-9">
                        <input type="text" class="form-control form-control-solid" name="code" value="{{ $item->code }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 ">
                    @lang('lang.cancel')</button>
                <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
                    @include('partials.general._button-indicator', [
                        'label' => trans('lang.save'),
                        'message' => trans('lang.sending'),
                    ])
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        KTApp.initSelect2();

        $("#save-new-item-code").appForm({
            onSuccess: function(response) {
                dataTableInstance.item.ajax.reload();
            },
        });
    });
</script>