    <form class="form" id="category-form" method="POST" action="{{ url("/days-off/save-dayoff-type") }}">
        <div class="card-body ">
            @csrf
            <input type="hidden" name="id" value="{{$dayoffType->id}}">
            <div class="form-group">
                <div class="mb-5">
                    <label for="name" class="required form-label">@lang('lang.title')</label>
                    <input type="text" value="{{ $dayoffType->name }}" autocomplete="off" name="name" class="form-control form-control-solid"
                        placeholder="Exemple : Mariage" data-rule-required="true"
                        data-msg-required="@lang('lang.required_input')" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-5">
                    <label class="col-form-label text-right col-lg-5">@lang('lang.type')</label>
                    <select class="form-select form-select-solid" name="type" data-hide-search="true"  data-rule-required="true" 
                    data-msg-required="@lang('lang.required_input')" data-control="select2" data-placeholder="Select an option">
                        <option value="0" disabled >-- @lang('lang.type') --</option>
                        <option @if($dayoffType->type === "daysoff" ) selected @endif value="daysoff">@lang('lang.daysoff')</</option>
                        <option @if($dayoffType->type === "permission" ) selected @endif value="permission">@lang('lang.permission')</</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="mb-5">
                    <label for="description" class="form-label">@lang('lang.description')</label>
                    <textarea name="description" data-rule-required="true" data-msg-required="@lang('lang.required_input')" autocomplete="off" class="form-control form-control-solid" rows="2"
                    placeholder="Exemple : Mariage des enfants...">{{ $dayoffType->description ?? '' }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="mb-5">
                    <label class="col-form-label text-right ">@lang('lang.impact_in_daysoff_balance')</label>
                    <select class="form-select form-select-solid" name="impact_in_dayoff_balance" data-hide-search="true"  data-rule-required="true" 
                    data-msg-required="@lang('lang.impact_in_daysoff_balance')" data-control="select2" data-placeholder="Select an option">
                    <option @if($dayoffType->impact_in_dayoff_balance == 0 ) selected @endif value=0>@lang('lang.no')</</option>
                    <option @if($dayoffType->impact_in_dayoff_balance == 1 ) selected @endif value=1>@lang('lang.yes')</</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <div class="mb-5">
                    <label for="nb_days" class=" form-label">@lang('lang.nb_days') </label>
                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="@lang('lang.nb_days_comma')" data-bs-original-title="" title=""></i>
                    <input  type="text" value="{{ $dayoffType->nb_days }}" autocomplete="off" name="nb_days" class="form-control form-control-solid"
                        placeholder="@lang('lang.nb_days')" data-rule-required="false"
                        data-msg-required="@lang('lang.required_input')" />
                </div>
            </div>
            <div class="form-group">
                <div class="mb-5">
                    <div class="form-check form-switch form-check-custom form-check-solid me-10">
                        <input class="form-check-input h-20px w-30px form-control" type="checkbox"  @if($dayoffType->enable || !isset($dayoffType->enable)) checked @endif name = "enable" value="1" id="enable"/>
                        <label class="form-check-label" for="enable">
                            Activé ce type de congé
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
        KTApp.initBootstrapPopovers();
        $("#category-form").appForm({
            onSuccess: function(response) {
                if (response.row_id) {
                    dataTableUpdateRow(dataTableInstance.daysOffType, response.row_id,response.data) 
                }else{
                    dataTableaddRowIntheTop(dataTableInstance.daysOffType ,response.data)
                }
            },
        })

    })
</script>
