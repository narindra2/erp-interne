<form class="form" id="project-modal-form" method="POST" action="{{ url('/project/save') }}">
    <div class="card-body">
        <div class="card card-flush shadow-sm ">
            <div class="card-body">
                @csrf
                <div class="form-group">
                    <div class="mb-3">
                        <label for="name" class="form-label require">Non du projet : </label>
                        <div class="input-group mb-5">
                            <span class="input-group-text" id="basic-addon1">
                                <span class="svg-icon svg-icon-2x">
                                    <i class="fas fa-list"></i>
                                </span>
                            </span>
                            <input type="text" id="name" class="form-control form-control-solid" value="" autocomplete="off"  data-rule-required="true" data-msg-required="@lang('lang.required')" name="name" placeholder="le nom projet" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-dark btn-sm mr-2 ">
            @lang('lang.cancel')
        </button>
        &nbsp;
        <button type="submit" id="submit"class=" btn btn-sm btn-light-success  mr-2">
            @include('partials.general._button-indicator', [
                'label' => "Ajouter",
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#project-modal-form").appForm({
            onSuccess: function(response) {
              dataTableaddRowIntheTop(dataTableInstance.projectMembersTable, response.data)
            },
        })
    });
</script>
