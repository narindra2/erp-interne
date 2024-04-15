
<div class="form-group row mt-2 py-4" class="marriedStatus">
    <label id="info"class="col-md-3 col-form-label">@lang('lang.marry')</label>
    <div class="col-md-6">
        <input class="form-control form-control-solid" value="{{ $user->marry_fullname }}" autocomplete="off" type="text" name="marry_fullname" />
    </div>
</div>
<div class="form-group row py-4" class="marriedStatus">
    <label  class="col-md-3 col-form-label">@lang('lang.marryDob')</label>
    <div class="col-md-3">
        <input class="form-control form-control-solid birthdate"
        @if(!empty($user->marry_place_of_birth))
            value="{{ convert_database_date($user->marry_place_of_birth) }}"
        @endif
        autocomplete="off" name="marry_birthdate" id="birthdate"/>
    </div>
    <label  class="col-md-3 col-form-label">@lang('lang.pob')</label>
    <div class="col-md-3">
        <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->marry_place_of_birth }}" type="text" name="marry_place_of_birth"/>
    </div>
</div>
<div class="form-group row" class="marriedStatus">
    <label  class="col-md-3 col-form-label">@lang('lang.marryCin')</label>
    <div class="col-md-3">
        <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->marry_CIN }}" type="text" name="marry_CIN"/>
    </div>
    <label  class="col-md-3 col-form-label">@lang('lang.cin_delivered')</label>
    <div class="col-md-3">
        <input class="form-control form-control-solid birthdate" autocomplete="off"
        @if(!empty($user->marry_cin_delivered))
            value="{{ convert_database_date($user->marry_cin_delivered) }}"
        @endif
        name="marry_cin_delivered" id="birthdate"/>
    </div>
</div>
<div class="form-group row py-4" class="marriedStatus">
    <label  class="col-md-3 col-form-label">@lang('lang.marryPhone_number')</label>
    <div class="col-md-6">
        <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->marry_phone_number }}" type="text" name="marry_phone_number"/>
    </div>
</div>
<div class="form-group row py-4" class="marriedStatus">
    <label  class="col-md-3 col-form-label">@lang('lang.marryEmail')</label>
    <div class="col-md-6">
        <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->marry_email }}" type="email" name="marry_email" data-msg-email="@lang('lang.required_email')"/>
    </div>
</div>
<div class="form-group row py-4" class="marriedStatus">
    <label  class="col-md-3 col-form-label">@lang('lang.marryJob')</label>
    <div class="col-md-6">
        <input class="form-control form-control-solid" autocomplete="off" value="{{ $user->marry_job }}" type="text" name="marry_job"/>
    </div>
</div>

