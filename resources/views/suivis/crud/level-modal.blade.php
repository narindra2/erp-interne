<form class="form" id="level-point-modal-form" method="POST" action="{{ url('/suivi/save-level/point') }}"> 
    <div class="card-body ">
        @csrf
        <div class="form-group">
            <div class="mb-5">
                <label for="type" class="form-label">Selectionner une version : </label>
                <select name="version_id" id="version" class="form-select form-select-solid rounded-start-0 border-start"
                    data-control="select2" data-hide-search="true" data-placeholder="Versions  ... "
                    data-dropdown-parent="#ajax-modal" data-allow-clear="true" data-hide-search="true">
                    <option value="0">-- @lang('lang.versions') --</option>
                    @foreach ($versions as $version)
                        <option value="{{ $version->id }}">{{ $version->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="separator mb-2"></div>
        <div class="rounded border p-5 " id="level-points"></div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 ">
            @lang('lang.fin')</button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
            @include('partials.general._button-indicator', [
                'label' => trans('lang.save'),
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#version").on("change", function() {
            let verisonId = $(this).val();
            let target = $("#level-points");
            let loading = `<div class="d-flex justify-content-center">
                                    <div class="spinner-border text-primary " style="width: 2rem; height: 2rem;" role="status">
                                    </div>
                                </div>`;
            target.html(loading);
            $.ajax({
                type: 'post',
                url: url("/suivi/load-level/point"),
                data: {
                    "version_id": verisonId,
                    "_token": _token
                },
                dataType: 'html',
                success: function(response) {
                    target.html(response);
                },
                error: function() {
                    target.html("Erreur");
                    console.log("error");
                }
            });
            $("#level-point-modal-form").appForm({
                isModal :false,
                forceBlock: true,
                onSuccess: function(response) {
                    $("#level-points").html(response.data);
                },
        })
        })
    })
</script>
