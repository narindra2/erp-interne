<form class="form" id="hour-recovery-form" method="POST" action="{{ url("/hour-recoveries") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{$hourRecovery->id}}">
        <input type="hidden" name="user_id" value="{{$hourRecovery->user_id}}">
        <input type="hidden" name="job_id" value="{{$hourRecovery->job_id}}">
        
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Nom</label>
            <div class="col-4">
                <input type="text" disabled class="form-control form-control-sm form-control-transparent" value="{{ $hourRecovery->user->name }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Prénom(s)</label>
            <div class="col-4">
                <input type="text" disabled class="form-control form-control-sm form-control-transparent" value="{{ $hourRecovery->user->firstname }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Matricule</label>
            <div class="col-4">
                <input type="text" disabled class="form-control form-control-sm form-control-transparent" value="{{ $hourRecovery->user->registration_number }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Fonction</label>
            <div class="col-4">
                <input type="text" disabled class="form-control form-control-sm form-control-transparent" value="{{ $hourRecovery->job->name }}">
            </div>
        </div>
        <div class="separator separator-dashed my-8"></div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4 required">Date d'absence</label>
            <div class="col-3">
                <input data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="date_of_absence" placeholder="DD/MM/YYYY" value="{{ $hourRecovery->date_of_absence ? $hourRecovery->date_of_absence->format("d/m/Y") : '' }}"/>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4 required">Durée d'absence (en heure)</label>
            <div class="col-2">
                <input type="number" data-rule-required="true" data-msg-required="@lang('lang.required_input')"  placeholder="Ex: 2 " class="form-control form-control-sm form-control-solid" autocomplete="off" name="duration_of_absence" value="{{ $hourRecovery->duration_of_absence }}"/>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4 required">Heure d'absence</label>
            <div class="col-2">
                <input type="text" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid" autocomplete="off" name="hour_absence" value="{{ $hourRecovery->hour_absence }}" placeholder="07h-09h"/>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4 required">Date de récupération</label>
            <div class="col-3">
                <input data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="recovery_start_date" placeholder="DD/MM/YYYY" value="{{ $hourRecovery->recovery_start_date ? $hourRecovery->recovery_start_date->format("d/m/Y") : '' }}"/>
            </div>
            <label class="col-form-label col-1">au</label>
            <div class="col-3">
                <input data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="recovery_end_date" placeholder="DD/MM/YYYY" value="{{ $hourRecovery->date_of_absence ? $hourRecovery->date_of_absence->format("d/m/Y") : '' }}"/>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4 required">Nature de la demande</label>
            <div class="col-7">
                <textarea data-rule-required="true" data-msg-required="@lang('lang.required_input')" name="description" placeholder="Nature de votre  demande ..." class="form-control form-control-sm form-control-solid" rows="3">{{ $hourRecovery->description ?? '' }}</textarea>
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

    function init_date(){
        var format = 'DD/MM/YYYY';
        var monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        var daysOfWeek =['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven','Sam'];
        var deliver = $(".datepicker").daterangepicker({
            singleDatePicker: true,
            // showDropdowns: true,
            drops: 'auto',
            autoUpdateInput: false,
            autoApply: true,
            locale: {
                defaultValue: "",
                format: format,
                applyLabel: "{{ trans('lang.apply') }}",
                cancelLabel: "{{ trans('lang.cancel') }}",
                daysOfWeek: daysOfWeek,
                monthNames: monthNames,
            },
        });
        deliver.on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format(format))
        });
    }
    init_date();

    $("#hour-recovery-form").appForm({
        onSuccess: function(response) {
            // dataTableaddRowIntheTop(dataTableInstance.hourRecovery ,response.data)
            dataTableInstance.hourRecovery.ajax.reload();
        },
    });
})
</script>
