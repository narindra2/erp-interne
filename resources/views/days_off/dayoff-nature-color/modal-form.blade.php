    <form class="form" id="dayoff-nature-form" method="POST" action="{{ url("/days-off/save-dayoff-nature") }}">
        <div class="card-body ">
            @csrf
            <input type="hidden" name="id" value="{{ $nature->id }}">
            <div class="form-group mb-5">
                    <label for="name" class="required form-label">Nature</label>
                    <input type="text" value="{{ $nature->nature }}" autocomplete="off" name="nature" class="form-control form-control-solid"
                        placeholder="Exemple : Vacance" data-rule-required="true"
                        data-msg-required="@lang('lang.required_input')" />
            </div>
            <div class="form-group mb-5">
                <label class="col-form-label text-right col-lg-5">Ajouté cette nature dans  : </label>
                <select class="form-select form-select-solid" name="type" data-hide-search="true"  data-rule-required="true" 
                        data-msg-required="@lang('lang.required_input')" data-control="select2" data-placeholder="Select an option">
                    <option value="0" disabled >-- @lang('lang.type') --</option>
                    @foreach ($types as $type)
                        <option @if($type["id"] == $nature->type ) selected @endif value="{{ $type["id"] }}"> {{ $type["text"] }}</option>
                    @endforeach
                </select>
            </div>
          
            <div class="form-group mb-5">
                <label for="color" class="form-label">Couleur indication</label>
                <div class="form-group mb-5">
                    <input type="color" class="form-control form-control-solid form-control-lg" id="color" name="color" value="{{ $nature->color ?? "#a219c8" }}" >
                </div>
            </div>
            <div class="form-group mb-5">
                    <div class="form-check form-switch form-check-custom form-check-solid me-10">
                        <input class="form-check-input h-20px w-30px form-control" type="checkbox"  @if($nature->status || !isset($nature->status)) checked @endif name = "status" value="1" id="status"/>
                        <label class="form-check-label" for="status">
                            Activé ce nature
                        </label>
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
        $("#dayoff-nature-form").appForm({
            onSuccess: function(response) {
                dataTableInstance.natureDaysOff.ajax.reload();
                // if (response.row_id) {
                //     dataTableUpdateRow(dataTableInstance.natureDaysOff, response.row_id,response.data) 
                // }else{
                //     dataTableaddRowIntheTop(dataTableInstance.natureDaysOff ,response.data)
                // }
            },
        })

    })
</script>
