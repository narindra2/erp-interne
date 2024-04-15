<form method="post" id="user-info-form"  action="{{ url("/user/info/update") }}">
<div class="card shadow-sm">
    @csrf
    <div class="card-body">
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
        <div class="form-group row mt-2">
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
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
            @include('partials.general._button-indicator', ['label' => trans('lang.mjr'),"message" => trans('lang.sending')])
        </button>
    </div>
</div>
</form>
<script>
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
    $(document).ready(function(){
        KTApp.initSelect2();
        var maritalStatus = $("#maritalStatus").val();
            if (maritalStatus == '2'){
                    $(".marriedStatus").css("display", "")
                }else{
                    $(".marriedStatus").css("display", "none")
                }
         
            init_date();
                $("#user-info-form").appForm({
                    isModal:false,
                    onSuccess:function(response){
                      
                    }
                });
            $("#maritalStatus").on('change', function(){
                if ($(this).val() == '2'){
                    $(".marriedStatus").css("display", "")
                }else{
                    $(".marriedStatus").css("display", "none")
                }
            })
    })
</script>