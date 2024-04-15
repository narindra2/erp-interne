<form class="form" id="publicHoliday-form" method="POST" action="{{ "/public-holidays" }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{$publicHoliday->id}}">
        
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Nom</label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="name" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" value="{{ $publicHoliday->name }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Date</label>
            <div class="col-6">
                <input data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="day" placeholder="DD/MM/YYYY" value="{{ $publicHoliday->day }}"/>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Durée (en heure)</label>
            <div class="col-6">
                <input data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid" autocomplete="off" name="duration" value="{{ $publicHoliday->duration }}"/>
            </div>
        </div>

    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
            @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" => trans('lang.sending')])
        </button>
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

    $("#publicHoliday-form").appForm({
        onSuccess: function(response) {
            if (response.row_id) {
                dataTableUpdateRow(dataTableInstance.publicHoliday, response.row_id,response.data) 
            }else{
                dataTableaddRowIntheTop(dataTableInstance.publicHoliday ,response.data)
            }
        },
    })

})
</script>
