<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#add-base-point">Le base des points</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#calcul-other-point-verison">Calcul pour les autres versions</a>
    </li>
</ul>
<div class="tab-content" id="TabContent">
    <div class="tab-pane fade show active" id="add-base-point" role="tabpanel">
        <form class="form" id="point-modal-form" method="POST" action="{{ url('/suivi/save-point-level') }}"> 
            <div class="card-body ">
                @csrf
                <div class="form-group">
                    <div class="mb-5">
                        <label for="type" class="form-label">Selectionner le type de client  : </label>
                        <select name="client_type_id" id="client_type_id" class="form-select form-select-solid rounded-start-0 border-start"
                            data-control="select2"  data-placeholder="Type de client  ... "
                            data-allow-clear="true" data-hide-search="true">
                            <option value="">-- @lang('lang.client_types') --</option>
                            @foreach ($client_types as $type)
                                <option 
                                @if ( $loop->index == 0 )
                                  selected
                                @endif value="{{ $type->id }}">{{ $type->name  }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div  class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <div class="mb-5">
                            <label for="type" class="form-label">Choississez le type de projet  : </label>
                            <select name="project_type_id" id="project_type_id" class="form-select form-select-solid rounded-start-0 border-start"
                                data-control="select2" data-placeholder="Type de projet  ... "
                                data-allow-clear="true" >
                                <option value="">-- @lang('lang.client_types') --</option>
                                @foreach ($project_types as $t)
                                    <option value="{{ $t->id }}">{{ $t->name  }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="mb-5">
                                <label for="type" class="form-label">Versions  : </label>
                                <select name="version_id" id="version_id_associed" class="form-select form-select-solid rounded-start-0 border-start"
                                    data-control="select2"  data-placeholder="Versions ... "
                                    data-allow-clear="true" data-hide-search="true">
                                    <option value="">-- @lang('lang.version') --</option>
                                    @foreach ($versions as $version)
                                        <option value="{{ $version->id }}">{{ $version->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="separator mb-2"></div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label for="niveau" class="form-label">Niveau</label>
                        <select name="niveau" id="level" class="form-select  form-select-solid rounded-start-0 border-start"
                        data-control="select2" data-placeholder="Niveau  ... "
                        data-allow-clear="true" data-hide-search="true"  >
                        <option value="" > -- Niveau -- </option>
                            @foreach ($levels as $level)
                                <option value="{{ $level["value"] }}">{{ $level["text"]  }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="point" class="form-label">@lang('lang.point') </label>
                        <input type="text" name="point" autocomplete="off" class="form-control form-control-solid col-md-3" value="" placeholder="Point value ... ">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="point_sup" class="form-label">@lang('lang.point_sup')</label>
                        <input type="text" name="point_sup" autocomplete="off" class="form-control  form-control-solid col-md-3" value="" placeholder="Point multi projet ... ">
                    </div>
                </div>
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
        <div class="card-footer ">
            <table id="pointClientTypeTable" class="table table-row-dashed table-row-gray-200 align-middle table-hover dataTable "></table>
        </div>
    </div>
    <div class="tab-pane fade" id="calcul-other-point-verison" role="tabpanel">
        <form class="form" id="version-other-point-modal-form" method="POST" action="{{ url('/suivi/save-point-other-version') }}"> 
            <div class="card-body ">
                @csrf
                <div class="separator mb-2"></div>
                <div class="row">
                    <div class="form-group col-md-8">
                        <div class="mb-5">
                            <h5  class="mb-2">Choississez la version  à calculer son point : </h5>
                            <select name="version_id_of_calcul" id="version_id_of_calcul" class="form-select form-select-solid rounded-start-0 border-start point-percentage"
                                data-control="select2" data-placeholder="Version  ... "data-dropdown-parent="#ajax-modal" data-allow-clear="true" >
                                <option value="">-- @lang('lang.version') --</option>
                                @foreach ($versions as $version)
                                    <option value="{{ $version->id }}">{{ $version->title  }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="mb-5">
                            <h5  class="mb-2">Montage : </h5>
                            <select name="montage" id="montage" class="form-select form-select-solid rounded-start-0 border-start point-percentage"
                                data-control="select2" data-placeholder="Montage ... "data-dropdown-parent="#ajax-modal" data-hide-search="true" data-allow-clear="true" >
                                <option value="">-- @lang('lang.montage') --</option>
                                @foreach ($montages as $montage)
                                    <option value="{{ get_array_value($montage,"value") }}">{{ get_array_value($montage,"text") }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="separator mb-2"></div>
                <div class="point-value-section">
                    <h5  class=" mb-2"><u>Choix 1 </u> : Utiliser directement un systeme point  : </h5>
                    <div class="mx-4">
                        <div class="mb-5 form-group col-md-6">
                            <label for="type" class="form-label">Point pour cette version  : </label>
                            <input type="text" name="point" id="point" autocomplete="off"  class="form-control form-control-solid" placeholder="Point ...." />
                        </div>
                    </div>
                </div>
                <div class=" point-percentage-section">
                    <h5  class="mb-2"><u>Choix 2 </u> :  Utiliser une pourcentage par raport au version base : </h5>
                    {{-- <i>Choississez la version calculer et le pourcentage par à partir du version de base</i> --}}
                    <div class="row mx-4 ">
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
                                <input type="text" name="percentage" id="percentage" autocomplete="off"  class="form-control form-control-solid point-percentage" placeholder="Pourcentage, ex: 12.5" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 ">
                    @lang('lang.fin')</button>
                <button type="submit" id="submit-other-point" class=" btn btn-sm btn-light-primary  mr-2">
                    @include('partials.general._button-indicator', [
                        'label' => trans('lang.save'),
                        'message' => trans('lang.sending'),
                    ])
                </button>
            </div>
        </form>
        <div class="card-footer ">
            <table id="versionsPointCalculTable" class="table table-row-dashed table-row-gray-200 align-middle table-hover dataTable "></table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        dataTableInstance.pointClientTypeTable = $("#pointClientTypeTable").DataTable({
            processing: true,
            dom : "frtp",
            ordering :false,
            columns:[
                {data :"client_types" , title: 'Types de client', "class":"text-left"},
                {data :"project_types" , title: 'Types de projet', "class":"text-left"},
                {data :"version" , title: 'version', "class":"text-left"},
                {data :"niveau" , title: 'Niveau', "class":"text-center fw-bold text-gray-900"},
                {data :"point" , title: 'point' ,"class":"text-center fw-bold text-gray-900"},
                {data :"point_sup" , title: 'Point supplementaire' ,"class":"text-center fw-bold text-gray-900"},
                {data :"created_at" , title: 'Date' ,"class":"text-center w-95 "},
                {data :"action" , title: '' ,"class":"text-center "},
            ],  
            ajax: {
                url: url("/suivi/data/points"),
                    data: function(data) {
                        data.client_type_id = $("#client_type_id").val();
                    }
                },
            language: {
                url: url("/library/dataTable/datatable-fr.json"),
            },
                    
        })
        dataTableInstance.versionsOtherPointTable = $("#versionsPointCalculTable").DataTable({
                    processing: true,
                    dom : "frtp",
                    ordering :false,
                    columns:[
                        {data :"version_name" , title: 'Nom du version', "class":"text-left"},
                        {data :"belongs" , title: 'Pour les', "class":"text-left"},
                        {data :"montage" , title: 'Montage', "class":"text-left"},
                        {data :"point" , title: 'point', "class":"text-left"},
                        // {data :"action" , title: ''},
                    ],  
                    ajax: {
                        url: url("/suivi/data/other-version-point"),
                    },
                    language: {
                        url: url("/library/dataTable/datatable-fr.json"),
                    },
                    
        })
        $("#client_type_id").on("change", function(){
            dataTableInstance.pointClientTypeTable.ajax.reload();
        });
        $(".point-percentage").on("change keyup", function(){
            if ($("#version_id_base").val() ||  $("#percentage").val()) {
                $(".point-value-section").addClass("d-none") ; 
                $("#point").val("");
            }else{
                $(".point-value-section").removeClass("d-none")
            }
        })
        $("#point").on("change keyup", function(){
            if ($(this).val()) {

                $("#version_id_base").val(""); $("#percentage").val("");
                $(".point-percentage-section").addClass("d-none")
                
            }else{
                $(".point-percentage-section").removeClass("d-none")
            }
        })
        $("#point-modal-form").appForm({
            isModal :false,
            forceBlock: true,
            onSuccess: function(response) {
                dataTableInstance.pointClientTypeTable.ajax.reload();
            },
        })
        $("#version-other-point-modal-form").appForm({
            isModal: false,
            forceBlock: false,
            submitBtn: "#submit-other-point",
            onSuccess: function(response) {
                dataTableInstance.versionsOtherPointTable.ajax.reload();
            },
        })
    
    })
</script>
