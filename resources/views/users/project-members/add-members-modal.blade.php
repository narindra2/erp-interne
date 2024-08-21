<form class="form" id="add-new-members-modal-form" method="POST" action="{{ url('/project/save/new-members') }}">
    <div class="card-body">
        <div class="card card-flush shadow-sm ">
            <div class="card-body">
                @csrf
                <input type="hidden" name="id" value="{{ $project->id }}">
                <div class="form-group">
                    <div class="mb-3">
                        <label for="name" class="form-label require">Non du projet ou groupe : </label>
                        <div class="input-group mb-5">
                            <span class="input-group-text" id="basic-addon1">
                                <span class="svg-icon svg-icon-2x">
                                    <i class="fas fa-toolbox"></i>
                                </span>
                            </span>
                            <input type="text" id="name" disabled readonly="true" class="form-control form-control-solid" value="{{ $project->name }}" autocomplete="off"  data-rule-required="true" data-msg-required="@lang('lang.required')" name="name" placeholder="le nom projet" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="mb-3 col-md-12">
                        <label for="users"> Collaborateurs :</label>
                        <select class="form-select" name="users[]" id="users" data-control="select2" data-close-on-select="false" data-placeholder="Selectionner les collaborateurs à ajouter" data-allow-clear="true" multiple="multiple">
                            <option value="0" disabled  >--Collaborateurs--</option>
                                @foreach ($users as $user)
                                    <option data-avatar= "{{  $user->avatarUrl }}" value="{{ $user->id }}">{{ $user->sortname}} ({{ $user->registration_number }}) </option>
                                @endforeach
                        </select>
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
        $("#add-new-members-modal-form").appForm({
            onSuccess: function(response) {
              dataTableUpdateRow(dataTableInstance.projectMembersTable,response.row_id,response.data)
            },
        })
        var optionFormat = function(item) {
                if ( !item.id ) {
                    return item.text;
                }
                var span = document.createElement('span');
                var imgUrl = item.element.getAttribute('data-avatar');
                var template = '';
                if (imgUrl) {
                    template += '<img src="' + imgUrl + '" class="rounded-circle h-25px me-2" alt="image"/>';
                }
                template += "   " +  item.text;
                span.innerHTML = template;
                return $(span);
            }

            $('#users').select2({
                templateSelection: optionFormat,
                templateResult: optionFormat
            });
    });
</script>
