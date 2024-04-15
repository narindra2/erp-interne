<form class="form" id="sanction-form" method="POST" action="{{ url("/users/sanctions/form-save") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{$sanction->id}}">
        <input type="hidden" name="user_id" value="{{$user_id}}">
    
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Type</label>
            <div class="col-6">
                <select name="type" id="type" class="form-control form-control-solid form-control-sm">
                    @for ($i = 0; $i < count($types); $i++)
                        <option value="{{ $i + 1}}" @if ($i + 1 == $sanction->type)
                            selected
                        @endif>{{ $types[$i] }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Motif</label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="reason" data-rule-required="true" 
                data-msg-required="@lang('lang.required_input')" value="{{ $sanction->reason }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Date</label>
            <div class="col-6">
                <input type="date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="date" value="@php if ($sanction->date) echo $sanction->date->format('Y-m-d') @endphp"/>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Durée (en jour)</label>
            <div class="col-6">
                <input type="number" min="0" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid" autocomplete="off" name="duration" value="{{ $sanction->duration }}"/>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-end">
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
            <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
                @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" => trans('lang.sending')])
            </button>
        </div>
    </div>
    
</form>
<script>
$(document).ready(function() {

    $("#sanction-form").appForm({
        onSuccess: function(response) {
            if (response.row_id) {
                dataTableUpdateRow(dataTableInstance.sanctionTable, response.row_id, response.data) 
            }else{
                dataTableaddRowIntheTop(dataTableInstance.sanctionTable, response.data);
            }
        },
    });

    // $("#deletePublicHoliday").on("click", function(e) {
    //     $.ajax({
    //         type: "POST",
    //         url: url("/public-holidays/delete/" + $(this).data("id")),
    //         data: {
    //             _token: _token
    //         },
    //         success: function (response) {
    //             $("#ajax-modal").modal().hide();
    //             dataTableInstance.publicHoliday.ajax.reload();
    //         }
    //     });
    // });

})
</script>
