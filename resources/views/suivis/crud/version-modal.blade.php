<form class="form" id="version-modal-form" method="POST" action="{{ url('/save/version') }}">
    <div class="card-body ">
        @csrf
        <div class="form-group">
            <div class="mb-5">
                <label for="version_suivi" class="form-label">Version : </label>
                <input type="text" name="version_suivi" id="version_suivi" autocomplete="off"  class="form-control form-control-solid" placeholder="Non du version" />
            </div>
        </div>
        <div class="separator mb-2"></div>
        <div class="mb-5">
            <label for="pole" class="form-label mb-5 ">Pôles affecté :  </label>
            <div class="mb-5 d-flex">
                @foreach ( App\Models\SuiviItem::$POLES as $pole)
                    <div class="form-check form-check-custom form-check-solid me-10">
                        <input class="form-check-input h-20px w-20pxpx" type="checkbox" name="poles[]" value="{{ get_array_value($pole,"value") }}" id="{{ get_array_value($pole,"value") }}">
                        <label class="form-check-label" for="{{ get_array_value($pole,"value") }}">{{ get_array_value($pole,"text") }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="separator mb-2"></div>
        {{-- <div class="row point-percentage-section">
            <div class="form-group col-md-6">
                <div class="mb-5">
                    <label for="type" class="form-label">Choississez la version de base  : </label>
                    <select name="version_id_base" id="version_id_base" class="form-select form-select-solid rounded-start-0 border-start point-percentage"
                        data-control="select2" data-placeholder="Version de base ... "data-dropdown-parent="#ajax-modal" data-allow-clear="true" >
                        <option value="">-- @lang('lang.version') --</option>
                        @foreach ($versions as $version)
                            <option value="{{ $version->id }}">{{ $version->title  }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-md-6 ">
                <div class="mb-5">
                    <label for="type" class="form-label">Pourcentage %  : </label>
                    <input type="text" name="percentage" id="percentage" autocomplete="off"  class="form-control form-control-solid point-percentage" placeholder="Pourcentage" />
                </div>
            </div>
        </div>
        <div class="form-group col-md-6 point-value-section">
            <div class="mb-5">
                <label for="type" class="form-label">Point pour ce version  : </label>
                <input type="text" name="point" id="point" autocomplete="off"  class="form-control form-control-solid" placeholder="Point ...." />
            </div>
        </div> --}}
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 ">
            @lang('lang.cancel')</button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
            @include('partials.general._button-indicator', [
                'label' => trans('lang.save'),
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>
    <div class="card-footer ">
        <table id="versionsTable" class="table table-row-dashed table-row-gray-200 align-middle table-hover dataTable "></table>
    </div>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        dataTableInstance.versionsTable = $("#versionsTable").DataTable({
                    processing: true,
                    dom : "frtp",
                    ordering :false,
                    columns:[
                        {data :"title" , title: 'Nom du version', "class":"text-left"},
                        {data :"belongs" , title: 'Pour les', "class":"text-left"},
                        {data :"creator" , title: 'Creer par', "class":"text-left"},
                        // {data :"point" , title: 'point', "class":"text-left"},
                        {data :"action" , title: ''},
                    ],  
                    ajax: {
                        url: url("/suivi/data/version"),
                    },
                    language: {
                        url: url("/library/dataTable/datatable-fr.json"),
                    },
                    
        })
        $("#version-modal-form").appForm({
            isModal: true,
            forceBlock: false,
            onSuccess: function(response) {
                $("#version_suivi").val("");
                if (response.data) {
                    // Add the new option  in select custom filter
                    dataTableaddRowIntheTop(dataTableInstance.versionsTable , response.data)
                }
            },
        })
    })
</script>
