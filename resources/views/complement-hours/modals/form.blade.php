<form class="form" id="complement-hours-form" method="POST" action="{{ url("/complement-hours") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{ $pointingResume->id }}">
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Date</label>
            <div class="col-6">
                <input name="day" type="date" value="{{ $pointingResume->getFormatDay() }}" class="form-control form-control-solid form-control-sm" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Employé(e)</label>
            <div class="col-8">
                <select name="registration_number" class="form-select form-select-solid form-control-sm"
                    data-rule-required="true" data-msg-required="@lang('lang.required_input')"
                    data-dropdown-parent="#ajax-modal" data-control="select2"
                    data-placeholder="Employé(e)" data-allow-clear="true">
                    <option disabled selected value="0"> -- Employé(e) --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->registration_number }}"
                            @if ($pointingResume->registration_number==$user->registration_number)
                                selected
                            @endif>{{ $user->registration_number . " - " . $user->fullname }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Type</label>
            <div class="col-8">
                <select name="additional_hour_type_id" class="form-select form-select-solid form-control-sm"
                    data-rule-required="true" data-msg-required="@lang('lang.required_input')"
                    data-dropdown-parent="#ajax-modal" data-control="select2"
                    data-placeholder="Employé(e)" data-allow-clear="true">
                    <option disabled selected value="0"> -- Type --</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}"
                            @if($pointingResume->additional_hour_type_id==$type->id)
                                selected
                             @endif
                            >{{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Durée (hh:mm)</label>
            <div class="col-4">
                <input name="minute_worked" type="text" value="{{ $pointingResume->getTimeWorks() }}" placeholder="02:00" class="form-control form-control-solid form-control-sm" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
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

    $("#complement-hours-form").appForm({
        onSuccess: function(response) {
            console.log(response);
            if (response.row_id) {
                dataTableUpdateRow(dataTableInstance.complementHour, response.row_id,response.data) 
            }else{
                dataTableaddRowIntheTop(dataTableInstance.complementHour ,response.data)
            }
        },
    });
})
</script>
