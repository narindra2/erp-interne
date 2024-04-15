    <form class="form" id="modal-form" method="POST" action="{{ "/modal/post" }}">
        <div class="card-body ">
            @csrf
            <div class="form-group">
                <div class="mb-10">
                    <label for="description" class="form-label">@lang('lang.description')</label>
                    <textarea name="description" autocomplete="off" class="form-control form-control-solid" rows="5"
                    placeholder="Description du categorie ..."></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="mb-10">
                    <label for="name" class="required form-label">@lang('lang.name')</label>
                    <input type="text" value="" autocomplete="off" name="name" class="form-control form-control-solid"
                        placeholder="Nom du categorie" data-rule-required="true"
                        data-msg-required="@lang('lang.required_input')" />
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
        $("#modal-form").appForm({
            onSuccess: function(response) {
                // dataTableaddRowIntheTop(dataTableInstance.categoryTable ,response.data)
            },
        })

    })
</script>  
  
    