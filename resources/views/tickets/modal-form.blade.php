<form class="form" id="tikect-modal-form" method="POST" action="{{ url('/ticket/add') }}">
    <div class="card-body ">
        @csrf
        <div class="form-group">
            <div class="mb-5">
                <label for="proprietor_id" class="form-label">@lang('lang.proprietor') Demandeur</label>
                <select name="proprietor_id" class="form-select form-select-solid form-control-lg"
                    data-rule-required="true" data-msg-required="@lang('lang.required_input')"
                    data-dropdown-parent="#ajax-modal" data-control="select2"
                    data-placeholder="@lang('lang.proprietor')" data-allow-clear="true">
                    <option disabled selected value="0"> -- Demandeur --</option>
                    @foreach ($from as $user)
                        <option value="{{ $user['value'] }}">{{ $user['text'] }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="separator mb-5"></div>
        <div class="form-group">
            <label for="proprietor_id" class="form-label ">Urgence</label>
            <div data-kt-buttons="true">
                @foreach (App\Models\TicketUrgence::drop(false, true) as $item) 
                    <label class="btn btn-outline btn-outline-dashed d-flex flex-stack text-start p-6 mb-5 active">
                        <div class="d-flex align-items-center me-2">
                            <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                <input class="form-check-input" type="radio" name="urgence_id" @if($loop->index == 0 ) checked @endif
                                    value="{{ $item['value'] }}" />
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="d-flex align-items-center fw-bolder flex-wrap">
                                    {!! $item["text"] !!}
                                </h6>
                            </div>
                        </div>
                    </label>  
                @endforeach
            </div>
        </div>
        <div class="separator mb-5"></div>
        <div class="form-group">
            <label for="description" class="form-label">@lang('lang.description')</label>
            <input type="text" list="suggestions" placeholder="Description du demande " name="description"
                autocomplete="off" multiple class="form-control form-control-lg form-control-solid"
                data-rule-required="true" data-msg-required="@lang('lang.required_input')">
            <datalist id="suggestions">
                <option disabled value="Suggestion">
                    @foreach ($suggestions as $suggestion)
                <option value="{{ $suggestion }}">
                    @endforeach
            </datalist>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 ">
            @lang('lang.cancel')</button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
            @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" =>
            trans('lang.sending')])
        </button>
    </div>
</form>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#tikect-modal-form").appForm({
            showAlertSuccess: true,
            onSuccess: function(response) {
                if (response.data) {
                    dataTableaddRowIntheTop(dataTableInstance.ticketsTable, response.data)
                }
            },
        })

    })
</script>
