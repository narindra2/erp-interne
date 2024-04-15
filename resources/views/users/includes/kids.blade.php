<div class="form-group row py-4">
    <label  class="col-3 col-form-label">@lang('lang.kids')</label>
    <div class="col-3">
        <div class="col-lg-4" id="add" onClick="add()">
            <a href="javascript:;" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
            <i class="la la-plus"></i></a>
        </div>
    </div>
    <label  class="col-3 col-form-label">@lang('lang.kids_bd')</label>
</div>
@if (count($user->kids))
    @foreach ($user->kids as $kid)
        <div class="form-group row mb-1 kids-input more_div" id="div">
            <div class="col-6">
                <input class="form-control form-control-solid" value="{{ $kid->fullname }}" autocomplete="off" type="text" name="kids_fullname[]"/>
            </div>
            <div class="col-3">
                <input class="form-control form-control-solid birthdate" value="{{ convert_database_date($kid->birthdate) }}" autocomplete="off" name="kids_birthdate_first[]" id="birthdate"/>
            </div>
            <button type="button" onClick="del(this)" class="btn btn-sm btn-icon btn-light-danger col-1 "><i class="la la-trash-o"></i></button>
        </div>
    @endforeach
@else
    <div class="form-group row mb-1 kids-input more_div" id="div">
        <div class="col-6">
            <input class="form-control form-control-solid" autocomplete="off" type="text" name="kids_fullname[]"/>
        </div>
        <div class="col-3">
            <input class="form-control form-control-solid birthdate" autocomplete="off" name="kids_birthdate_first[]" id="birthdate"/>
        </div>
        <button type="button" onClick="del(this)" class="btn btn-sm btn-icon btn-light-danger col-1 "><i class="la la-trash-o"></i></button>
    </div>
@endif
