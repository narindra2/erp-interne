<div class="card shadow-sm">
    <div class="card-body">
   
        <div class="form-group row py-4">
            <label  class="col-md-2 col-form-label required">@lang('lang.contract_type')</label>
            <div class="col-md-3">
                <select  disabled class="form-select form-select-solid" name="contract_type_id"  data-hide-search="true" data-control="select2" data-placeholder="Select an option">
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
                <input disabled class="form-control form-control-transparent birthdate" autocomplete="off"
                @if(!empty($user->hiring_date))
                    value="{{ convert_database_date($user->hiring_date) }}"
                @endif
                name="hiring_date" data-rule-required="true" />
            </div>
        </div>
        <div class="form-group row py-4">
            <label  class="col-md-2 col-form-label required">@lang('lang.salary')</label>
            <div class="col-md-3">
                <input disabled class="form-control form-control-transparent" autocomplete="off"
                @if(!empty($user->userJob->salary))
                    value="{{ $user->userJob->salary }}"
                @endif
                type="number" name="salary" data-rule-required="true"
                />
            </div>
        </div>
        <div class="form-group row">
            <label  class="col-md-2 col-form-label">@lang('lang.regulation')</label>
            <div class="col-md-3">
                <input disabled class="form-control form-control-transparent" autocomplete="off" value="{{ $user->regulation }}" type="text" name="regulation" />
            </div>
            <label  class="col-md-2 col-form-label">@lang('lang.account_number')</label>
            <div class="col-md-5">
                <input  disabled class="form-control form-control-transparent" autocomplete="off" value="{{ $user->account_number }}" type="text" name="account_number"/>
            </div>
        </div>
        {{-- <div class="form-group row py-4">
            <label  class="col-md-2 col-form-label required">@lang('lang.nb_days_off')</label>
            <label  class="col-md-2 col-form-label required">@lang('lang.role')</label>
            <div class="col-md-5 mt-2">
                <select disabled class="form-select form-select-solid" name="user_type_id" data-hide-search="true" data-control="select2" data-placeholder="Select an option">
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
        </div> --}}
        <div class="form-group row py-4">
            <label  class="col-md-2 col-form-label required">@lang('lang.department')</label>
            <div class="col-md-3">
                <select disabled class="form-select form-select-solid" name="department_id" data-hide-search="true" data-control="select2" data-placeholder="Select an option" data-rule-required="true" >
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
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        KTApp.initSelect2();
        
    })
</script>