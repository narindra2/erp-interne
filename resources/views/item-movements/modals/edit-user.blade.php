<form class="form" id="edit_user" method="POST" action="{{ url('/item-movements/save-edit-user') }}">
    @csrf
    <div class="card">
        <div class="card-body card-scroll h-300px">
            <div class="card-body">
                <input type="hidden" name="id" value="{{ $itemMovement->id }}">
                <div class="h-300px">
                    @foreach ($userJobs as $userJob)
                        <div class="fv-row mb-10">
                            <label class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="users[]" value="{{ $userJob->users_id }}" 
                                @if ($itemMovement->isUsedByUser($userJob->users_id))
                                    checked
                                @endif 
                                />
                                <span class="form-check-label fw-bold text-gray-700 fs-6">{{ $userJob->user->getNameAndRegistrationNumber() }}</span>
                            </label>
                        </div>
                    @endforeach
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
</form>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        KTApp.initBootstrapPopovers();

        $("#edit_user").appForm({
            onSuccess: function(response) {
                if (response.row_id) {
                    dataTableUpdateRow(dataTableInstance.assign, response.row_id, response.data)
                } else {
                    dataTableaddRowIntheTop(dataTableInstance.assign, response.data)
                }
            },
        });
    })
</script>
