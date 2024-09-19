<form action="{{ url("/days-off/giveResult") }}" method="POST" id="modal-form-info">
    <div class="card-body">
        @csrf
        <input type="hidden" name="id" value="{{ $dayOff->id }}" id="id">
       @if($dayOff->is_canceled)
            <h3 class="text-danger text-center mb-4">Cette demande a été annulée.</h3>
       @endif
       <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Type d'absence :</span>
                    <div class="d-flex align-items-center">
                        <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  $dayOff->type->getType() }}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Durrée(s) :</span>
                    <div class="d-flex align-items-center">
                        <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{ $dayOff->duration }} jour(s)</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Demandé le :</span>
                    <div class="d-flex align-items-center">
                        <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  $dayOff->created_at->translatedFormat("d M Y") }}</span>
                    </div>
                </div>
            </div>
       </div>
       <div class="separator border-info mt-3 mb-3"></div>
        <div class="form-group row mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-title d-flex flex-column">   
                                <span class="text-gray-700 pt-1 fw-semibold fs-6">Congé de :</span>
                                <div class="d-flex align-items-center">
                                    <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  $dayOff->applicant->fullname }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-title d-flex flex-column">   
                                <span class="text-gray-700 pt-1 fw-semibold fs-6">Solde de congé  : </span>
                                <div class="d-flex align-items-center">
                                    <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{ $dayOff->applicant->nb_days_off_remaining }} jour(s) </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-title d-flex flex-column">   
                                <span class="text-gray-700 pt-1 fw-semibold fs-6">Permission  :</span>
                                <div class="d-flex align-items-center">
                                    <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2"> 
                                        @php
                                            $max_permission = App\Models\DayOff::$_max_permission_on_year;
                                            $permission_total =App\Models\User::get_cache_total_permission( $dayOff->applicant_id);
                                        @endphp
                                        <div class="fs-2 fw-bolder"></div>
                                            {{ $max_permission - $permission_total }} / {{ $max_permission ." jr(s)" }}
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="separator border-info mt-3 mb-3"></div>
                    <div class="row">
                        <div class="col-md-9">
                            @if ($dayOff->applicant_id != $dayOff->author_id)
                                    <div class="card-title d-flex flex-column">   
                                        <span class="text-gray-700 pt-1 fw-semibold fs-6">Demandé par : </span>
                                        <div class="d-flex align-items-center">
                                            <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{ $dayOff->author->fullname }} </span>
                                        </div>
                                    </div>
                                @endif
                        </div>
                        @if($dayOff->result == "validated")
                            <div class="col-md-3">
                                <div class="card-title d-flex flex-column">   
                                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Action : </span>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-bold  me-2 lh-1 ls-n2"> 
                                            @php
                                                echo anchor( url("/dayOff/export/pdf/$dayOff->id/0"), "Exporté en pdf",  ["title" => "Exporté en pdf",  "class"=> "text-info"]);
                                            @endphp 
                                            </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="col-form-label ">Date début : </label>
                        <div class="row">
                            <div class="col-md-6">
                                <select name="start_date_is_morning" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez le type de congé" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                                    <option value="1" @if ($dayOff->start_date_is_morning == 1)  selected  @endif>De la matinée du</option>
                                    <option value="0" @if ($dayOff->start_date_is_morning == 0) selected @endif> Après-midi du</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input id="start_date" name="start_date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" value="{{ $dayOff->getStartDate()->format('d/m/Y') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="col-form-label ">Date de retour : </label>
                        <div class="row">
                            <div class="col-md-6">
                                <input id="return_date" name="return_date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" value="{{ $dayOff->getReturnDate()->format('d/m/Y') }}"/>
                            </div>
                            <div class="col-md-6">
                                <select name="return_date_is_morning" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez le type de congé" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                                    <option value="1" @if ($dayOff->return_date_is_morning == 1)  selected  @endif>la matinée </option>
                                    <option value="0" @if ($dayOff->return_date_is_morning == 0) selected @endif> après-midi </option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                </div>
                @php
                    $absence = $dayOff->type->type == "daysoff" ? " du congé"  :" de la permission";
                @endphp
                <div class="separator border-info mt-3 mb-3"></div>
                <div class="row">
                    <div class="col-md-6">
                        <span class="text-gray-700 pt-1 fw-semibold fs-6">Type {{  $absence }} : </span>
                        <select id="type_id" name="type_id" class="form-select form-select-lg form-select-solid"  data-control="select2" data-hide-search="false" data-dropdown-parent="#ajax-modal">
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @if ($dayOff && $dayOff->type_id == $type->id) selected @endif>{{ $type->name . ($type->nb_days ?   " - $type->nb_days"  . " jrs" : "") }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <span class="text-gray-700 pt-1 fw-semibold fs-6">Nature {{  $absence }} : </span>
                            <select id="nature_id" name="nature_id" class="form-select form-select-lg form-select-solid" value="{{ $dayOff->nature->nature }}" data-control="select2" data-hide-search="true" data-dropdown-parent="#ajax-modal">
                                @foreach ($natures as $item)
                                    <option value="{{ $item->id }}" @if ($dayOff && $dayOff->nature_id == $item->id) selected @endif>{{ $item->nature }}</option>
                                @endforeach
                            </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card-title d-flex flex-column">   
                        <span class="text-gray-700 pt-1 fw-semibold fs-6">Description : </span>
                        <textarea id="reason" class="form-control form-control form-control-solid" rows="2" data-kt-autosize="true" data-rule-required="true" data-msg-required="@lang('lang.required_input')">{{ $dayOff->reason }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-title d-flex flex-column">   
                                <span class="text-gray-700 pt-1 fw-semibold fs-6  mb-1">Piéce(s) justificatif :</span>
                                @if ($dayOff->attachments->count())
                                    <div class="row">
                                        @foreach ($dayOff->attachments as $attachment) 
                                            <span class="me-3 col-md-1"><a href="{{ url('days-off/download-attachment') . "/" . $attachment->id }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset(theme()->getMediaUrlPath() . 'svg/files/upload.svg') }}" alt="" data-toggle="tooltip" data-placement="bottom" title="{{ $attachment->filename }}" height="40px" width="40px"></a></span>
                                        @endforeach 
                                    </div>
                                @else 
                                    <i class="mt-2" >Pas de piéce justificatif ajouté.</i>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-title d-flex flex-column">   
                                <span class="text-gray-700 pt-1 fw-semibold fs-6 mb-1">Ajouté autre Piéce joint : </span>
                               <input class="form-control form-control-sm" id="formFileSm" name="files[]" type="file" multiple>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="separator border-info mt-3 mb-3"></div>
                <span class="text-gray-700 pt-1 fw-semibold fs-6 mb-1">Statut  :</span>
                <div data-kt-buttons="true">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="btn btn-sm btn-outline  btn-active-light-primary d-flex flex-stack text-start">
                                <div class="d-flex align-items-center me-2">
                                    <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                        <input class="form-check-input" @if ($dayOff->result == "in_progress") checked  @endif type="radio" name="result" value="in_progress"/>
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
                            <label class="btn btn-sm btn-outline  btn-active-light-success d-flex flex-stack text-start">
                                <!--end::Description-->
                                <div class="d-flex align-items-center me-2">
                                    <!--begin::Radio-->
                                    <div class="form-check form-check-custom form-check-solid form-check-success me-6">
                                        <input class="form-check-input" @if ($dayOff->result == "validated") checked  @endif type="radio" name="result" value="validated"/>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="d-flex align-items-center fs-4 fw-bold flex-wrap">
                                            Accepté/Validé
                                        </h4>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="btn btn-sm btn-outline  btn-active-light-danger d-flex flex-stack text-start">
                                <div class="d-flex align-items-center me-2">
                                    <div class="form-check form-check-custom form-check-solid form-check-danger me-6">
                                        <input class="form-check-input" @if ($dayOff->result == "refused") checked @endif type="radio" name="result" value="refused"/>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="d-flex align-items-center fs-4 fw-bold flex-wrap">
                                            Refusé
                                        </h4>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                @if ($dayOff->result == "validated" && !$dayOff->is_canceled)
                    <div class="separator border-info  mt-7 mb-3"></div>
                    <div class="row">
                        <div class="form-check form-check-custom form-check-warning form-check-solid ">
                            <label class="form-check-label" for="">
                                - Cocher ceci si vous voulez <span class="text-danger"> annuler </span> cette demande  :  &nbsp;
                            </label>
                            <input class="form-check-input border-warning" type="checkbox" name="is_canceled" value="1" @if ($dayOff->is_canceled) cheked @endif />
                            
                        </div>
                    </div>
                    <style>
                        .border-warning{
                            border: 2px solid #F1BC00 !important;
                        }
                    </style>
                    <div class="separator border-info mt-3 mb-3"></div>
                @endif
        </div>
            <div class="d-flex justify-content-end my-5">
                <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn  btn-sm btn-secondary  font-weight-bold mx-2 ">Quitter ...</button>
                @php
                    /* Old concept */
                    $can_update_it = false;
                    if (!$dayOff->getStartDate()->isPast()) {
                        $can_update_it = true;
                    }
                    if ($dayOff->getReturnDate()->isPast() && !$dayOff->getReturnDate()->isToday() ) {
                        $can_update_it = false;
                    }
                    /* End old concept */

                    /* new demande RH  */
                    if ($dayOff->is_canceled) {
                        $can_update_it = false;
                    }else {
                        $can_update_it = true; 
                    }
                @endphp
                @if ($can_update_it)
                    <button type="submit" class="btn btn-sm btn-light-primary font-weight-bold mx-2">
                        @include('partials.general._button-indicator', ['label' =>"Enregistrer" ,"message" => trans('lang.sending')])
                    </button>
                @endif
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#modal-form-info").appForm({
            onSuccess: function(response) {
                //dataTableUpdateRow(dataTableInstance.dayOffRequested , response.row_id, response.data)
                if (dataTableInstance.my_days_off) {
                    dataTableInstance.my_days_off.ajax.reload();
                }
                if (dataTableInstance.dayOffRequested) {
                    dataTableInstance.dayOffRequested.ajax.reload();
                    // reload gantt 
                    loadGantt();
                }
            },
        });

        function init_date(){
                var format = 'DD/MM/YYYY';
                var monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                var daysOfWeek =['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven','Sam'];
                var deliver = $(".datepicker").daterangepicker({
                    singleDatePicker: true,
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
    });
</script>