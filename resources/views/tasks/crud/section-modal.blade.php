<form class="form" id="task-section-modal-form" method="POST" action="{{ url('/task/section/save') }}">
    <div class="card-body">
        <div class="card card-flush shadow-sm ">
            <div class="card-body">
                @csrf
                <input type="hidden" name="section_id" value="{{ $section->id ?? 0 }}">
                <div class="form-group">
                    <div class="mb-3">
                        <label for="title" class="form-label require">@lang('lang.name') : </label>
                        <div class="input-group mb-5">
                            <span class="input-group-text" id="basic-addon1">
                                <span class="svg-icon svg-icon-2x">
                                    <i class="fas fa-list"></i>
                                </span>
                            </span>
                            <input type="text" id="title" class="form-control form-control-solid" value="{{ $section->title ?? null }}" autocomplete="off"  data-rule-required="true" data-msg-required="@lang('lang.required')" name="title" placeholder="@lang('lang.name') du section ex: Tech" />
                        </div>
                    </div>
                </div>
                
                @if (!$section->id)
                    <div class="form-group">
                        <label for="users" class="form-label ">Ajouter des membres : </label>
                        <div class="input-group mb-5">
                            <span class="input-group-text">
                                <span class="svg-icon svg-icon-2x">
                                    <i class="fas fa-users"></i>
                                </span>
                            </span>
                            @include('tasks.kanban.users-tag', ['users' => $members ,"placeholder" => ""])
                        </div>
                    </div> 
                     
                @endif
                <label for="permissions" class="form-label mb-2 ">Permissions aux membres  : </label><br>
                <label class="mb-3 " > <i> Chequer les access permis .</i>  </label>
                    @foreach ($permissions as $permission)
                        <div class="form-group mb-4">
                            <div class="form-check form-check-custom form-check-success form-check-solid form-check-sm">
                                <input class="form-check-input" type="checkbox" @if ($section->id && in_array(get_array_value($permission, "access"),( $section->permissions ?? []))) checked @endif value="1" name="{{ get_array_value($permission, "access")   }}" id="{{ get_array_value($permission, "access")}}"/>
                                <label class="form-check-label {{ get_array_value($permission, "danger") ? "text-danger" :"" }}" for="{{ get_array_value($permission, "access") }}">
                                    {{ get_array_value($permission, "description") }}
                                </label>
                            </div>
                        </div> 
                    @endforeach
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
                'label' => $section->id ? 'Mettre à jour' : "Créer",
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#task-section-modal-form").appForm({
            onSuccess: function(response) {
                if (response.update != "0") {
                    $("#section-title-"+ response.update).text(response.title)
                }else{
                    $(".nav-item-section-task:last").before(response.data);
                }
            },
        })
    });
</script>
