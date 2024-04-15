<form action="{{ url('/messaging/groups/store') }}" id="messageGroupForm" method="POST">
    <div class="card-body">
        @csrf
        {{-- <input type="hidden" name="id" value="{{$publicHoliday->id}}"> --}}
        
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Nom</label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="name" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" placeholder="Nom du groupe">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-end">
            {{-- @if ($publicHoliday->id != null)
                <button type="button" id="deletePublicHoliday" data-bs-dismiss="modal" aria-label="Close" data-id="{{ $publicHoliday->id }}" class="btn btn-light-danger btn-sm mr-2 "> @lang('lang.delete')</button>
            @endif --}}
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
            <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
                @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" => trans('lang.sending')])
            </button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $("#messageGroupForm").appForm({
            onSuccess: function(response) {
                $("#group_list").append(response.group)
            },
        });
    });
</script>