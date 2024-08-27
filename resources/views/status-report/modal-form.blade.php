<form class="form" id="modal-form-status-report" method="POST" action="{{ url("/save/status-report") }}">
   <div class="card-body">
       @csrf
       <input type="hidden" name="id" value="{{ $statusReport->id }}">
           <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <label class=" text-gray-700 pt-1 fw-semibold fs-6">Type de rapport</label>
                    <div class="d-flex align-items-center">
                    <select id="type_status_report_id" name="type_status_report_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez type de rapport">
                        <option value="null"    disabled >Type de rapport</option>
                        @foreach ($type as $t)
                            <option value="{{  $t["id"] }}" @if ($t["id"] == $statusReport->type_status_report_id ) selected @endif >{{ $t["text"] }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
               <div class="col-md-6">
                   <div class="card-title d-flex flex-column input-group">   
                       <label class="text-gray-700 pt-1 fw-semibold fs-6">Employé(e)</label>
                       <select id="user_id" name="user_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal"  data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                           <option disabled selected >-- Employé --</option>
                           @foreach ($users as $user)
                               <option value="{{ $user->id }}" @if ($user->id == $statusReport->user_id ) selected @endif>{{ $user->fullname }}</option>
                           @endforeach
                       </select>
                   </div>
               </div>
          </div>
       <div class="separator border-info mt-3 mb-3"></div>
           <div class="form-group col-md-12">
               <label class="text-gray-700 pt-1 fw-semibold fs-6 ">Date de rapport : </label>
               <div class="row">
                   <div class="col-md-4">
                       <select name="start_date_is_morning" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Date de rapport" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                           <option value="1" @if ($statusReport->start_date_is_morning == 1 || !$statusReport->id )  selected  @endif>La matinée du</option>
                           <option value="0" @if ($statusReport->start_date_is_morning === 0) selected @endif> Après-midi du</option>
                       </select>
                   </div>
                   <div class="col-md-4">
                       <input id="" class="form-control form-control-sm form-control-solid datepicker" 
                           @if ($statusReport->start_date) value="{{ $statusReport->start_date->format("d/m/Y") }}" @endif autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')"/>
                   </div>
                   <div class="col-md-4">
                       
                    <div class="input-group mb-5">
                        <input type="search" class="form-control form-control-solid" autocomplete="off" placeholder="Heure ..." value="{{ $statusReport->time_start }}" name ="time_start" id="time_start" />
                        <span class="input-group-text to-link deleteTime"  data-time="#time_start" title="Enlever l'heure">x</span>
                    </div>
                          
                   </div>
               </div>
           </div>
           <div class="form-group col-md-12">
               <label class="text-gray-700 pt-1 fw-semibold fs-6 ">Fin de rapport : </label>
               <div class="row">
                <div class="col-md-4">
                    <select name="fin_date_is_morning" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Fin du rapport" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                        <option value="-1" @if ($statusReport->fin_date_is_morning == -1 || !$statusReport)  selected  @endif>Fin de rapport</option>
                        <option value="1" @if ($statusReport->fin_date_is_morning == 1  )  selected  @endif>la matinée </option>
                        <option value="0" @if ($statusReport->fin_date_is_morning === 0) selected @endif> après-midi </option>
                    </select>
                </div>
                   <div class="col-md-4">
                       <input id="fin_date" name="fin_date"  class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="false" data-msg-required="@lang('lang.required_input')" @if ($statusReport->fin_date)
                       value="{{ $statusReport->fin_date->format("d/m/Y") }}"
                   @endif />
                   </div>
                   
                   <div class="col-md-4">
                    <div class="input-group mb-5">
                        <input type="search" class="form-control form-control-solid" autocomplete="off" placeholder="Heure ..." value="{{ $statusReport->time_fin }}" name="time_fin" id="time_fin" />
                        <span class="input-group-text to-link deleteTime"  data-time="#time_fin" title="Enlever l'heure">x</span>
                    </div>
                </div>
               </div>
           </div>
       <div class="separator border-info mt-3 mb-3"></div>
       <div class="row">
           
           <div class="col-md-12">
               <div class="form-group  mb-4">
                   <label class=" text-gray-700 pt-1 fw-semibold fs-6">Rapport detail</label>
                   <textarea data-rule-required="false" data-msg-required="@lang('lang.required_input')" name="report" class="form-control form-control form-control-solid" data-kt-autosize="true">{{ $statusReport->detail }}</textarea>
               </div>
           </div>
       </div>
       <div class="separator border-info mt-3 mb-4"></div>
       <span class="text-gray-700 pt-1 fw-semibold fs-6 mb-2">Statut de rapport :</span>
       <div data-kt-buttons="true">
        <div class="row">
            <div class="col-md-4">
                <label class="btn btn-sm btn-outline  btn-active-light-primary d-flex flex-stack text-start">
                    <div class="d-flex align-items-center me-2">
                        <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                            <input class="form-check-input" @if ($statusReport->status == "in_progress") checked  @endif type="radio" name="status" value="in_progress"/>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="d-flex align-items-center fs-4 fw-bold flex-wrap">
                                En cours 
                            </h4>
                        </div>
                    </div>
                </label>
            </div>
            
            <div class="col-md-4">
                <label class="btn btn-sm btn-outline  btn-active-light-danger d-flex flex-stack text-start">
                    <div class="d-flex align-items-center me-2">
                        <div class="form-check form-check-custom form-check-solid form-check-danger me-6">
                            <input class="form-check-input" @if ($statusReport->status == "unjustified" || !$statusReport->id) checked  @endif type="radio" name="status" value="unjustified"/>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="d-flex align-items-center fs-4 fw-bold flex-wrap">
                               Non justifié
                            </h4>
                        </div>
                    </div>
                </label>
            </div>
            <div class="col-md-4">
                <label class="btn btn-sm btn-outline  btn-active-light-success d-flex flex-stack text-start">
                    <!--end::Description-->
                    <div class="d-flex align-items-center me-2">
                        <!--begin::Radio-->
                        <div class="form-check form-check-custom form-check-solid form-check-success me-6">
                            <input class="form-check-input" @if ($statusReport->status == "validated") checked  @endif type="radio" name="status" value="validated"/>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="d-flex align-items-center fs-4 fw-bold flex-wrap">
                                Justifié
                            </h4>
                        </div>
                    </div>
                </label>
            </div>
        </div>
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
   $("#modal-form-status-report").appForm({
       onSuccess: function(response) {
           if (response.success) {
               dataTableInstance.statusReport.ajax.reload();
           }
       },
   });
   function init_date(){
       var format = 'DD/MM/YYYY';
       var monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
       var daysOfWeek =['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven','Sam'];
       var minDateCollaborator = new Date('{{ now()->addDays(7) }}');
       var date = $(".datepicker").daterangepicker({
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
       date.on('apply.daterangepicker', function(ev, picker) {
           $(this).val(picker.startDate.format(format))
       });
   }
   init_date(); 
   $("#time_start , #time_fin").flatpickr({
        enableTime: true,
        noCalendar: true,
        time_24hr: true,
        dateFormat: "H:i",
    });


    $(".deleteTime").on("click",function(){
        let id = $(this).attr("data-time");
        console.log(id);
        $(id).val("");
    })
});
</script>  

