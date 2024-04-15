<form action="{{ url("/item-movements/new-save") }}" method="POST" id="newMvt">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="form-group row">
                <div class="form-group row mb-5">
                    <label class="col-form-label col-3">Article</label>
                    <div class="col-9">
                        <select id="item_id" name="item_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal">
                            <option selected >-- Article --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->getNameAndCode() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-5">
                    <label class="col-form-label col-3">Lieu</label>
                    <div class="col-9">
                        <select id="location_id" name="location_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal">
                            <option selected >--Lieu--</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-5">
                    <label class="col-form-label col-3">Statut</label>
                    <div class="col-9">
                        <select id="item_status_id" name="item_status_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal">
                            <option selected >--Statut--</option>
                            @foreach ($itemStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
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

        $("#newMvt").appForm({
            onSuccess: function(response) {
                dataTableInstance.assign.ajax.reload();
            },
        });
    });
</script>