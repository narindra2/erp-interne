<x-base-layout>
    @include('users.includes.header')
    <div class="card card-flush shadow-sm">
        @if ($user->id==null)
            <form action="{{ url("/user/store") }}" id="user_form" method="POST">
        @else
            <form action="{{ url("/user/edit/$user->id") }}" id="user_form" method="POST">
        @endif
            @csrf
            @if ($user->id!=null)
                <input type="hidden" value="{{ $user->id }}" name="user_id">
            @endif
            <div class="card-body">
                <div class="form-group mb-8">
                <h3 class="card-title">@lang('lang.user_info')</h3>
                    <div class="form-group row py-4">
                        <label  class="col-3 col-form-label required">@lang('lang.registration')</label>
                        <div class="col-6">
                            <input class="form-control form-control-solid" type="text"
                            @if ($user->id!=null)
                                value="{{ $user->registration_number }}"
                            @endif
                            name="registration_number" data-rule-required="true"
                            data-msg-required="@lang('lang.required')" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group row py-4">
                        <label  class="col-md-3 col-form-label required">@lang('lang.name')</label>
                        <div class="col-md-6">
                            <input class="form-control form-control-solid" type="text" value="{{ $user->name }}" autocomplete="off" name="name" data-rule-required="true"
                            data-msg-required="@lang('lang.required')"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-md-3 col-form-label">@lang('lang.lastname')</label>
                        <div class="col-md-6">
                            <input class="form-control form-control-solid" autocomplete="off" type="text" value="{{ $user->firstname }}" name="firstname" />
                        </div>
                    </div>
                    <div class="form-group row py-4">
                        <label  class="col-md-3 col-form-label required">@lang('lang.sex')</label>
                        <div class="col-md-6">
                            <select class="form-select form-select-solid" name="sex" data-hide-search="true" data-control="select2" data-placeholder="Select an option">
                                <option value="1" <?php if($user->sex == 1){ ?>selected<?php } ?> >@lang('lang.man')</option>
                                <option value="0" <?php if($user->sex == 0){ ?>selected<?php } ?> >@lang('lang.woman')</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row py-4">
                        <label  class="col-md-3 col-form-label required">@lang('lang.dob')</label>
                        <div class="col-md-3">
                            <input class="form-control form-control-solid birthdate" autocomplete="off"
                            @if(!empty($user->birthdate))
                                value="{{ convert_database_date($user->birthdate) }}"
                            @endif
                            name="birthdate" id="birthdate" data-rule-required="true"
                            data-msg-required="@lang('lang.required')"/>
                        </div>
                        <label  class="col-md-3 col-form-label required">@lang('lang.pob')</label>
                        <div class="col-md-3">
                            <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->place_of_birth }}" type="text" name="place_of_birth" data-rule-required="true"
                            data-msg-required="@lang('lang.required')"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-md-3 col-form-label">@lang('lang.cin')</label>
                        <div class="col-md-3">
                            <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->CIN }}" type="text" name="cin"/>
                        </div>
                        <label  class="col-md-3 col-form-label">@lang('lang.cin_delivered')</label>
                        <div class="col-md-3">
                            <input class="form-control form-control-solid birthdate" autocomplete="off"
                            @if(!empty($user->cin_delivered))
                                value="{{ convert_database_date($user->cin_delivered) }}"
                            @endif
                            name="cin_delivered" id="birthdate"/>
                        </div>
                    </div>
                    <div class="form-group row py-4">
                        <label  class="col-md-3 col-form-label required">@lang('lang.job')</label>
                        <div class="col-md-6">
                            <select class="form-select form-select-solid" name="jobs_id" data-hide-search="true" data-control="select2" data-placeholder="Select an option">
                                @foreach ($jobs as $job)
                                    <option value="{{ $job->id }}"
                                        @if(!empty($user->userJob))
                                            @if($user->userJob->jobs_id==$job->id)
                                                selected
                                            @endif
                                        @endif
                                        >{{ $job->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row py-4">
                        <label  class="col-md-3 col-form-label required">@lang('lang.address')</label>
                        <div class="col-md-6">
                            <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->address }}" type="text" name="address" data-rule-required="true"
                            data-msg-required="@lang('lang.required')"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-md-3 col-form-label required">@lang('lang.phone_number')</label>
                        <div class="col-md-6">
                            <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->phone_number }}" type="text" name="phone_number" data-rule-required="true"
                            data-msg-required="@lang('lang.required')"/>
                        </div>
                    </div>
                    <div class="form-group row py-4">
                        <label  class="col-md-3 col-form-label required">@lang('lang.email')</label>
                        <div class="col-md-6">
                            <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->email }}" type="email" name="email" data-rule-required="true"
                            data-msg-required="@lang('lang.required')" data-msg-email="@lang('lang.required_email')"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-md-3 col-form-label required">@lang('lang.marital_status')</label>
                        <div class="col-md-6">
                            <select class="form-select form-select-solid" id="maritalStatus" name="marital_status_id" data-hide-search="true" data-control="select2" data-placeholder="Select an option">
                                @foreach ($maritalStatuses as $maritalStatus)
                                <option value="{{ $maritalStatus->id }}"
                                    @if(!empty($user->maritalStatus))
                                        @if($user->maritalStatus->id==$maritalStatus->id)
                                            selected
                                        @endif
                                    @endif
                                    >{{ $maritalStatus->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @include('users.includes.married')
                    <div class="form-group row py-4">
                        <label  class="col-md-3 col-form-label required">@lang('lang.father')</label>
                        <div class="col-md-6">
                            <input class="form-control form-control-solid" autocomplete="off" type="text" value="{{ $user->father_fullname }}" name="father_fullname" data-rule-required="true"
                            data-msg-required="@lang('lang.required')"/>
                        </div>
                    </div>
                    <div class="form-group row py-4">
                        <label  class="col-md-3 col-form-label required">@lang('lang.mother')</label>
                        <div class="col-md-6">
                            <input class="form-control form-control-solid" autocomplete="off" type="text" value="{{ $user->mother_fullname }}" name="mother_fullname" data-rule-required="true"
                            data-msg-required="@lang('lang.required')"/>
                        </div>
                    </div>
                    @include('users.includes.kids')
                </div>
                <div class="separator separator-dashed my-8"></div>
                <h3 class="card-title">Informations emploi</h3>
                <div class="form-group row py-4">
                    <label  class="col-md-2 col-form-label required">@lang('lang.local')</label>
                    <div class="col-md-2">
                        <select class="form-select form-select-solid" name="local"  data-hide-search="true" data-control="select2" data-placeholder="Select an option">
                            @foreach ($locals as $local)
                                <option value="{{ $local['id'] }}"
                                    @if(!empty($user->userJob))
                                        @if($user->userJob->local==$local['id'])
                                            selected
                                        @endif
                                    @endif
                                    >{{ $local['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row py-4">
                    <label  class="col-md-2 col-form-label required">@lang('lang.category')</label>
                    <div class="col-md-1">
                        <select class="form-select form-select-solid" name="category"  data-hide-search="true" data-control="select2" data-placeholder="Select an option">
                            @foreach ($categories as $category)
                                <option value="{{ $category }}"
                                    @if(!empty($user->userJob))
                                        @if($user->userJob->category==$category)
                                            selected
                                        @endif
                                    @endif
                                    >{{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2"></div>
                    <label  class="col-md-2 col-form-label required">@lang('lang.group')</label>
                    <div class="col-md-1">
                        <select class="form-select form-select-solid" name="group"  data-hide-search="true" data-control="select2" data-placeholder="Select an option">
                            @foreach ($groups as $group)
                                <option value="{{ $group }}"
                                    @if(!empty($user->userJob))
                                        @if($user->userJob->group==$group)
                                            selected
                                        @endif
                                    @endif
                                    >{{ $group }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row py-4">
                    <label  class="col-md-2 col-form-label required">@lang('lang.contract_type')</label>
                    <div class="col-md-3">
                        <select class="form-select form-select-solid" name="contract_type_id"  data-hide-search="true" data-control="select2" data-placeholder="Select an option">
                            @foreach ($contractTypes as $contractType)
                                <option value="{{ $contractType->id }}"
                                    @if(!empty($user->userJob))
                                        @if($user->userJob->contract_type_id==$contractType->id)
                                            selected
                                        @endif
                                    @endif
                                    >{{ $contractType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row py-4">
                    <label  class="col-md-2 col-form-label required">@lang('lang.hiring_date')</label>
                    <div class="col-md-3">
                        <input class="form-control form-control-solid birthdate" autocomplete="off"
                        @if(!empty($user->hiring_date))
                            value="{{ convert_database_date($user->hiring_date) }}"
                        @endif
                        name="hiring_date" data-rule-required="true" data-msg-required="@lang('lang.required')"/>
                    </div>
                </div>
                <div class="form-group row py-4">
                    <label  class="col-md-2 col-form-label required">@lang('lang.salary')</label>
                    <div class="col-md-3">
                        <input class="form-control form-control-solid" autocomplete="off"
                        @if(!empty($user->userJob->salary))
                            value="{{ $user->userJob->salary }}"
                        @endif
                        type="number" name="salary" data-rule-required="true"
                        data-msg-required="@lang('lang.required')"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label  class="col-md-2 col-form-label">@lang('lang.regulation')</label>
                    <div class="col-md-3">
                        <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->regulation }}" type="text" name="regulation" />
                    </div>
                    <label  class="col-md-2 col-form-label">@lang('lang.account_number')</label>
                    <div class="col-md-5">
                        <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->account_number }}" type="text" name="account_number"/>
                    </div>
                </div>
                <div class="form-group row py-4">
                    <label  class="col-md-2 col-form-label required">@lang('lang.nb_days_off')</label>
                    <div class="col-md-3">
                        <input class="form-control form-control-solid" autocomplete="off" type="number" step="any" name="nb_days_off_remaining" value="{{ $user->nb_days_off_remaining }}" min="0" data-rule-required="true"
                        data-msg-required="@lang('lang.required')"/>
                    </div>
                    <label  class="col-md-2 col-form-label required">@lang('lang.role')</label>
                    <div class="col-md-5 mt-2">
                        <select class="form-select form-select-solid" name="user_type_id" data-hide-search="true" data-control="select2" data-placeholder="Select an option">
                            @foreach ($userTypes as $userType)
                                <option value="{{ $userType->id }}"
                                    @if(!empty($user->type))
                                        @if($user->type->id==$userType->id)
                                            selected
                                        @endif
                                    @endif
                                    >{{ $userType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row py-4">
                    <label  class="col-md-2 col-form-label required">@lang('lang.department')</label>
                    <div class="col-md-3">
                        <select class="form-select form-select-solid" name="department_id" data-hide-search="true" data-control="select2" data-placeholder="Select an option" data-rule-required="true" data-msg-required="@lang('lang.required')">
                            <option disabled selected>--@lang('lang.department')--</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    @if(!empty($user->userJob->department))
                                        @if($user->userJob->department->id==$department->id)
                                            selected
                                        @endif
                                    @endif
                                    >{{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                   
                    <div class="col-md-4 mt-2">
                        <div class="form-check form-check-custom form-check-solid ">
                            <label class="form-check-label " for="is_cp">
                                C'est un CP ? ( Cocher si Oui )
                            </label>
                            &nbsp;&nbsp;
                            <input class="form-check-input" type="checkbox" value='1' name="is_cp" @if ( $user->userJob && $user->userJob->is_cp) checked @endif  id="is_cp"/>
                            
                        </div>
                    </div>
                </div>
                <div class="separator separator-dashed my-8"></div>
                <div class="row">
                    <div class="col-5">
                    </div>
                    <div class="col-7">
                        <button type="submit" id="submit" class="btn btn-primary mr-2">@lang('lang.save')</button>
                    </div>
                </div>       
                @if ($user_id)
                    @include('users.includes.sanction', compact('user'))
                @endif
            </div>
        </form>
    </div>
    @section('scripts')
        <script>
             var max = 1
             function del(params) {
                if(max > 1){
                    max--
                    $(params).closest('.kids-input').remove()
                }
            }
            function add() {
                if(max < 6 ){
                    max++
                    $(".more_div").eq(0).clone()
                    .find("input").val("").end()
                    .insertAfter(".more_div:last");
                    init_date();
                }
            }
            function init_date(){
                var format = 'DD/MM/YYYY';
                var monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                var daysOfWeek =['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven','Sam'];
                var deliver = $(".birthdate").daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    drops: 'auto',
                    autoUpdateInput: false,
                    autoApply: false,
                    maxDate: new Date(),
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

            $(document).ready(function() {
                init_date();
                let userId = "{{ $user_id }}";
                console.log(userId)
                $("#user_form").appForm({
                    isModal:false,
                    onSuccess:function(response){
                       // window.location.replace("{{ url("/users") }}");
                    }
                });

                var maritalStatus = $("#maritalStatus").val();
                if (maritalStatus == '2'){
                        $(".marriedStatus").css("display", "")
                    }else{
                        $(".marriedStatus").css("display", "none")
                    }
                $("#maritalStatus").on('change', function(){
                    if ($(this).val() == '2'){
                        $(".marriedStatus").css("display", "")
                    }else{
                        $(".marriedStatus").css("display", "none")
                    }
                })

                if (userId) {
                    dataTableInstance.sanctionTable = $("#sanctionTable").DataTable({
                    processing: true,
                    columns: [ 
                        {data :"date" , title: 'Date'},
                        {data :"reason" , title: 'Motif'},
                        {data: "type", title: 'Type'},
                        {data: "duration", title: 'Durée (en jour)'},
                        {data :"actions"},
                    ],
                    ajax: {
                        url: url("/users/sanctions-data/" + userId),
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                });
                }
            })
        </script>
    @endsection

</x-base-layout>
