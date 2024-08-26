@if ($can_make_request)
     <form class="form" id="modal-form" method="POST" action="{{ url("/days-off/store-request") }}">
        <div class="card-body">
            @csrf
            <input type="hidden" name="id" value="{{ $dayOff->id }}">
            <input type="hidden" name="applicant_id" value="{{ $dayOff->applicant_id ?? null }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="card-title d-flex flex-column">   
                        <label class="text-gray-700 pt-1 fw-semibold fs-6">Congé de :</label>
                        <div class="d-flex align-items-center">
                            @if (isset($dayOff->id))
                                <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  $dayOff->applicant->fullname }}</span>
                             @else 
                                <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  auth()->user()->fullname }}</span>
                             @endif

                        </div>
                    </div>
                </div>
                @if( !isset($dayOff->id) ||  (isset($dayOff->id) && $dayOff->applicant->id == auth()->id()))
                    <div class="col-md-4">
                        <div class="card-title d-flex flex-column">   
                            <label class="text-gray-700 pt-1 fw-semibold fs-6">Solde de congé :</label>
                            <div class="d-flex align-items-center">
                                <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  auth()->user()->nb_days_off_remaining }}</span>
                            </div>
                        </div>
                    </div>
                @endif
               
                </div>
            <div class="separator border-info mt-3 mb-3"></div>
            {{-- <div class="form-group row">
                <label class="col-3 text-gray-700 pt-1 fw-semibold fs-6 mb-4">Type de la demande</label>
                <div class="col-4">
                    <select id="type" name="request_type" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez le type de congé">
                        <option value="daysoff"
                        @if ($dayOff->type_id == 1)
                            checked
                        @endif>Congé</option>
                        <option value="permission">Permission</option>
                        @if ($dayOff->type_id == 2)
                            checked
                        @endif>
                    </select>
                </div>
                <div class="col-4">
                    <select id="category" name="type_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez la catégorie" data-rule-required="true" data-msg-required="@lang('lang.required_input')"> </select>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card-title d-flex flex-column">   
                        <label class=" text-gray-700 pt-1 fw-semibold fs-6">Type de la demande</label>
                        <div class="d-flex align-items-center">
                            @php
                                $request_type = "daysoff";
                                if ($dayOff->id) {
                                    $request_type =  $dayOff->type->type;
                                }
                            @endphp
                            <select id="type" name="request_type" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez le type de congé">
                                <option value="daysoff" @if ($request_type == "daysoff") selected @endif>Congé</option>
                                <option value="permission" @if ($request_type == "permission")selected  @endif> Permission</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-title d-flex flex-column">   
                        <span class="text-gray-700 pt-1 fw-semibold fs-6">Type : </span>
                        <div class="d-flex align-items-center input-group">
                            <select id="category" name="type_id" class=" form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez la catégorie" data-rule-required="true" data-msg-required="@lang('lang.required_input')"> </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator border-info mt-3 mb-3"></div>
            {{-- <div class="form-group row mb-4">
                <label class="col-3 text-gray-700 pt-1 fw-semibold fs-6">Date de début</label>
                <div class="col-4">
                    <input id="" class="form-control form-control-sm form-control-solid datepicker" @if ($dayOff->start_date)
                        value="{{ $dayOff->start_date->format("d/m/Y") }}"
                    @endif autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')"/>
                </div>
                <div class="col-4">
                    <select name="start_date_is_morning" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez le type de congé" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                        <option value="1" @if ($dayOff->start_date_is_morning == 1)
                            checked
                        @endif>Matinée</option>
                        <option value="0" @if ($dayOff->start_date_is_morning == 0)
                            checked
                        @endif>Après-midi</option>
                    </select>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label class="col-3 text-gray-700 pt-1 fw-semibold fs-6">Date de retour</label>
                <div class="col-4">
                    <input data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker"  @if ($dayOff->return_date)
                        value="{{ $dayOff->return_date->format("d/m/Y") }}"
                    @endif autocomplete="off" name="return_date" placeholder="DD/MM/YYYY" />
                </div>
                <div class="col-4">
                    <select data-rule-required="true" data-msg-required="@lang('lang.required_input')" name="return_date_is_morning"class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez le type de congé">
                        <option value="1" @if ($dayOff->return_date_is_morning == 1)
                            checked
                        @endif>Matinée</option>
                        <option value="0" @if ($dayOff->return_date_is_morning == 0)
                            checked
                        @endif>Après-midi</option>
                    </select>
                </div>
            </div> --}}
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="text-gray-700 pt-1 fw-semibold fs-6 ">Date début : </label>
                    <div class="row">
                        <div class="col-md-6">
                            <select name="start_date_is_morning" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez le type de congé" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                                <option value="1" @if ($dayOff->start_date_is_morning == 1 || !$dayOff->id )  selected  @endif>De la matinée du</option>
                                <option value="0" @if ($dayOff->start_date_is_morning === 0) selected @endif> Après-midi du</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input id="" class="form-control form-control-sm form-control-solid datepicker" 
                                @if ($dayOff->start_date)value="{{ $dayOff->start_date->format("d/m/Y") }}" @endif autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')"/>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label class="text-gray-700 pt-1 fw-semibold fs-6 ">Date de retour : </label>
                    <div class="row">
                        <div class="col-md-6">
                            <input id="return_date" name="return_date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" @if ($dayOff->return_date)
                            value="{{ $dayOff->return_date->format("d/m/Y") }}"
                        @endif />
                        </div>
                        <div class="col-md-6">
                            <select name="return_date_is_morning" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez le type de congé" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                                <option value="1" @if ($dayOff->return_date_is_morning == 1 || !$dayOff)  selected  @endif>la matinée </option>
                                <option value="0" @if ($dayOff->return_date_is_morning === 0) selected @endif> après-midi </option>
                            </select>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="separator border-info mt-3 mb-3"></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group  mb-4">
                        <label class=" text-gray-700 pt-1 fw-semibold fs-6">Nature de l' absence</label>
                        <select name="nature_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Choisissez la nature de congé" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                            <option  value="0" @if ( !$dayOff ) selected   @endif  >- Nature de la demande - </option>
                                @foreach ($natures as $nature)
                                    <option value="{{  $nature->id}}" @if ( $nature->id == $dayOff->nature_id  ) selected   @endif>{{  $nature->nature}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group  mb-4">
                        <label class=" text-gray-700 pt-1 fw-semibold fs-6">Description  de la demande</label>
                        <textarea data-rule-required="true" data-msg-required="@lang('lang.required_input')" name="reason" class="form-control form-control form-control-solid" data-kt-autosize="true">{{ $dayOff->reason }}</textarea>
                    </div>
                </div>
            </div>
            <div class="separator border-info mt-3 mb-3"></div>
            {{-- <div class="form-group row mb-6">
                <label class="col-lg-3 text-gray-700 pt-1 fw-semibold fs-6 text-lg-right">Fichier joint</label>
                <div class="col-8">
                    <input class="form-control form-control-sm" id="formFileSm" name="files[]" type="file" multiple>
                  </div>
            </div> --}}

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
            <div class="separator border-info mt-3 mb-4"></div>
            @if (!$dayOff->id && auth()->user()->isRhOrAdmin() )
                <div class="form-group mb-4" >
                    <div class="form-check form-check-custom form-check-success form-check-solid  form-check-sm">
                        <input class="form-check-input green-border" type="checkbox" value="true" name="validate_immediately"  id="validate-in-creation" />
                        <label class="form-check-label me-2" for="validate-in-creation">
                        <u> Valider immédiatement cette demande</u> ? (cochez si oui  )
                        </label> 
                    </div>
                    <style>
                        .green-border{
                            border: 2px solid #47BE7D !important;
                        }

                    </style>
                </div> 
            @endif
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
        $("#modal-form").appForm({
            onSuccess: function(response) {
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
        $("#type").on("change", function(e) {
            e.preventDefault();
            changeValueSelect($("#type").val());
        });

        function changeValueSelect(type="daysoff" , selected = 0)
        {
            $.ajax({
                type: "GET",
                url: url("/days-off/update-select-form"),
                data: {
                    "type": type,
                    "_token": _token,
                    "selected" : selected 
                },
                success: function (response) {
                    $("#category").empty();
                    $("#category").append(response.data);
                }
            });
        }
        changeValueSelect("{{ $request_type }}"  , '{{ $dayOff->type_id ?? 0 }}' ); 
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
                @if (!$auth->isHR() && !$auth->isAdmin())
                    minDate:minDateCollaborator,
                @endif
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

        $("#applicant_id").on("change", function() {
            let applicantID = $(this).val();
            let users = {!! json_encode($users) !!};
            users.forEach(user => {
                if (user.id == applicantID) {
                    let balance = user.nb_days_off_remaining;
                    $("#showBalance").val( balance.toString());
                }
            });
        });
    });
</script>  
    @else
    <form class="form"  method="POST" >
        <div class="card-body ">
                <div class="alert alert-dismissible bg-light-danger d-flex flex-center flex-column py-10 px-10 px-lg-20 mb-10">
                    <span class="svg-icon svg-icon-5tx svg-icon-danger mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"></rect>
                            <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black"></rect>
                            <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black"></rect>
                        </svg>
                    </span>
                    <div class="text-center text-dark">
                        <h1 class="fw-bolder mb-5">@lang('lang.max_dayoff_executed')</h1>
                        <div class="separator separator-dashed border-danger opacity-25 mb-5"></div>
                        <div class="mb-9"></strong>.
                    </div>
                </div>
            </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
        </div>
    </form>
    @endif
    