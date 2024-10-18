    <div class="card shadow-sm  mb-1  " >
        <div class="card-header border-1">
            <div class="me-2 card-title align-items-start ">
                {{-- <span class="card-label  fs-3 mb-1 mt-1"> Tableau </span> --}}
            </div>
            <div class="card-toolbar  ">
                    @if ($auth->isM2pOrAdmin() || $auth->isCp() )
                        @include('suivis.crud.crud-btn')
                    @endif
                     &nbsp;
                    <form id="new-record-suivi" method="POST" action="{{ url('/suivi/add-row') }}" >
                        @csrf
                        <input type="hidden" name="suivi_item_id" id="suivi_item_id" value="0">
                        <div class="row">
                            <div class="col-md-12 w-300px">
                                <select name="folder_id" data-ajax--url="{{ url('/search/folder') }}" data-ajax--cache="true"
                                    data-minimum-input-length="3" data-language="fr" data-placeholder="Rechercher dossier "
                                    data-allow-clear="true" data-formatResult='optionFormat' data-formatSelection='optionFormat'
                                    data-control="select2" class="form-select form-select-solid form-select-sm " id="folder_id">
                                    <option value="0" selected>Rechercher dossier</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" id="submit-clone-row" class=" btn btn-sm btn-light-primary"
                                    title="Ajouter sur le tableau">
                                    @include('partials.general._button-indicator', [
                                        'label' => '<i class="fas fa-plus"></i>',
                                        'message' => "",
                                    ])
                                </button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
<div class="card shadow-sm">
    <div class="card-body py-3">
        <div class="d-flex justify-content-end mb-3">
            <div class="filter-datatable">
                @include('filters.filters-basic', [
                    'inputs' => $basic_filter,
                    'filter_for' => 'suiviTable',
                ])
            </div>
            &nbsp;
        </div>
        @if ($auth->isM2pOrAdmin())
            <div class="mx-2 mt-5">
                @php
                    $model = App\Models\SuiviColumnCustomed::class;
                    $hiddenedColones = App\Models\SuiviColumnCustomed::get_user_hidden_columns_array();
                @endphp
                Colonnes : 
                @foreach ($model::$TABLE_ALLOWED_COLUMNS as $key => $columns)
                    @if (!in_array($key,$model::$NOT_CUSTOMABLE_COLUMNS))
                        <a class="columns-visibility {{ in_array($key,$hiddenedColones) ? "text-gray-500" : "" }}" data-column="{{ $key }}">{{ $columns }}</a> @if(!$loop->last)  -  @endif
                    @endif
                @endforeach
            </div>
        @endif
        <div class="d-flex justify-content-end mb-1">
            <input type="text" id="search_suivi" 
                autocomplete="off"class="form-control form-control-solid form-select-sm w-200px mx-2 d-none"
                placeholder="{{ trans('lang.search') . ' sur la table' }}">
            @include('suivis.crud.custom-filter-dropdown', ['options' => $options])
            <a id="do-search-suivi" title="Recharger"
                class="d-flex align-items-end mb-3 btn btn-sm  btn-outline-dashed ">
                <i class="fas fa-sync-alt fs-5"></i>
            </a>
        </div>
        {{-- <div class="table-responsive h-900px"> --}}
        <div class="table-responsive"  id="table-responsive">
            <table id="suiviTable" width="100%" class=" table align-middle  cell-border table-row-gray-500  gy-1  ">
                <tbody class="child"></tbody>
                <tfoot>
                    <tr>
                        @for ($i = 0; $i < $count_header; $i++)
                            @if ($duration_footer == $i )
                                <td id="total_duration" class="justify-content-end"></td>
                            @else
                                <td></td>
                            @endif
                        @endfor
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<style>
    .is-invalid .select2-selection,
    .needs-validation~span>.select2-dropdown {
        border-color: red !important;
    }
    .columns-visibility{
        cursor: pointer;
    }
    
    .child{
        cursor: move;
    }
    .blockui .blockui-overlay {
        transition: all 0.3s ease;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(1.4px);
        position: fixed;
    }
</style>

@section('dynamic_link')
    <link rel="stylesheet" href="{{ asset('dataTable/cell-color.min.css') }}">
    <link rel="stylesheet" href="{{ asset("library/context-menu/jquery-contextMenu.min.css") }}">
    <link rel="stylesheet" href="{{ asset("library/jquery-confirm/jquery-confirm.min.css") }}">
    <link rel="stylesheet" href="{{ asset("dataTable/fixed-column.min.css") }}">
@endsection
@section('dynamic_script')
    <script src="{{ asset("library/context-menu/jquery-contextMenu.min.js") }}"></script>
    <script src="{{ asset("library/context-menu/jquery-ui-position.min.js") }}"></script>
    <script src="{{ asset("library/jquery-confirm/jquery-confirm.min.js") }}"></script>
    <script src="{{ asset("dataTable/fixed-column.min.js")  }}"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
           
            var groupColumn = "{{ $row_group }}";
            var hiddenColumns =  @json($hidden_headers);
            var backgroundRowEdit = "bg-secondary";
            var finishedId = {{ $finished_id }};
            var pausedId = {{ $paused_id }};
            var columnCount = {{ $total_duration  }};
            var hiddenedColoneUser =  @json($hiddened ?? []); 
            var blockLoader = '<div class="blockui-message"><span class="spinner-border text-primary"></span> Un instant svp ... <span ></span></div>'
            var blockTableSuivi = document.querySelector("#table-responsive");
            var blockUITableDiv = new KTBlockUI(blockTableSuivi, { message: blockLoader, });
            function secondsToDhmsItem(seconds) {
                return secondsToDhms(seconds); // view/includes/helper-js
            }
            
            dataTableInstance.suiviTable = $("#suiviTable")
            .on('preXhr.dt', function (e, settings, json, xhr) {
                    blockUITableDiv.block();
            }).on('xhr.dt', function (e, settings, data) {
                    blockUITableDiv.release();
            }).DataTable({
                processing: true,
                ordering:false,
                fixedHeader: true,
                stateSave: true,
                fixedColumns: {
                    left: 3,
                    right: 2
                },
                paging: false,
                // dom: "tr",
                // dom: "itr",
                dom: "it", // proccecing est remplace par blockUITableDiv
                columnDefs: [{
                    "targets":hiddenColumns,
                    "visible": false,
                    "searchable": false,
                    "className": 'fw-semibold fs-6 text-gray-800 border-bottom'
                }, ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                },
                columns: @json($headers),
                ajax: {
                    url: url("/suivi/data_list"),
                    data: function(data) {
                        <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                             data.{{ $input }} = $("#{{ $input }}").val();
                        <?php } ?>
                        /** Combinaison filter*/
                        data.users = $("#user_ids").val();
                        data.suivis = $("#folder_ids").val();
                        data.type_project = $("#type_project").val();
                        data.versions = $("#version_ids").val();
                        data.montages = $("#montage_ids").val();
                        data.status = $("#status_ids").val();
                        data.poles = $("#poles_ids").val();
                    },

                },
                preDrawCallback: function () {
                    $("#suiviTable_processing").css("z-index", "10").addClass("bg-dark")
                  
                },
                footerCallback: async function(row, data, start, end, display) {
                    var api = this.api();
                    var pageTotal = api
                        .column(columnCount, { page: 'current' })
                        .data()
                        .reduce(function(a, b) {
                            return parseInt(a) + parseInt(b);
                        }, 0);
                    // Update footer
                    $("#total_duration").html("<span class ='text-info text-left fs-3'>" + secondsToDhmsItem(pageTotal) + "</span>");
                  
                },
                drawCallback: function (settings) {
                    var api = this.api();
                    var rows = api.rows().nodes();
                    var last = null;
                    api.column(groupColumn)
                        .data()
                        .each(function (group, i) {
                            if (last !== group ) {
                                $(rows)
                                    .eq(i)
                                    .before('<tr class="group  h-20px"><td colspan="'+(columnCount + 2) +'"> ' + group + '</td></tr>');
                                last = group;
                            }
                        });
                    
                },
                initComplete: function(settings, json) {
                    @if (!$auth->isM2pOrAdmin() && !$auth->isCp())
                     $("#search_suivi").before(json.pause_btn);
                    @endif

                    let table = settings.oInstance.api();
                    if (json.hidden_columns) {
                        table.columns(json.hidden_columns).visible(false);
                    }
                    dataTableShowRowDetails("#suiviTable", dataTableInstance.suiviTable,"details-row"); /** includes/helpers-js*/
            
                    $('a.columns-visibility').on('click', function (e) {
                            e.preventDefault();
                            let index = $(this).attr('data-column')
                            let column = dataTableInstance.suiviTable.column(index);
                            column.visible() ?  $(this).addClass("text-gray-500") : $(this).removeClass("text-gray-500");
                            column.visible(!column.visible());
                            $.ajax({
                                url: url("/suivi/save/custom-visible-column"),
                                type: 'POST',
                                dataType: 'json',
                                data: {"column_rang" : index ,"_token" :_token},
                                success: function(result) {
                                    if (result.success) {
                                        return true
                                    }
                                    return false
                                },
                                error: function(request, status, error) {
                                    
                                }
                            });
                    })
                }
            });
            @if ($auth->isM2pOrAdmin() || $auth->isCp()) 
                $(document).on('dblclick', ".edit-item-all", function(e) {
                    edit_row(this, true)
                }); 
            @endif

            function showLoadingTable(e=!0){e?$("#suiviTable_processing").css("display",""):$("#suiviTable_processing").css("display","none")}let startX,scrollLeft,mouseDown=!1;const slider=document.querySelector(".table-responsive"),startDragging=e=>{mouseDown=!0,startX=e.pageX-slider.offsetLeft,scrollLeft=slider.scrollLeft},stopDragging=e=>{mouseDown=!1},move=e=>{if(e.preventDefault(),!mouseDown)return;const t=e.pageX-slider.offsetLeft-startX;slider.scrollLeft=scrollLeft-t};function reloadTable(){if(loopMinutors)for(var e in loopMinutors)clearInterval(loopMinutors[e]);dataTableInstance.suiviTable.ajax.reload()}function edit_row(e,t=!1){forceShowColomunsForEdit();var i=$(e).attr("data-id");$("#suivi_row_"+i).find("select, input").each((function(e){("true"==$(this).attr("data-can-edit")||t)&&doEditInput(this)})),$(".row-detail-"+i).each((function(e){("true"==$(this).attr("data-can-edit")||t)&&doEditInput(this)})),$("#save-item-"+i).css("display",""),$("#suivi_row_"+i).addClass(getClassToEditColor(i))}function getClassToEditColor(e){$("#class-row-color-"+e).data("row-color");return backgroundRowEdit}function doEditInput(e){if($(e).hasClass("form-control")&&$(e).removeClass("form-control-transparent"),$(e).hasClass("form-select")){$(e).removeClass("form-select-transparent");var t={templateResult:optionFormat,dir:document.body.getAttribute("direction"),minimumResultsForSearch:"true"==e.getAttribute("data-hide-search")?1/0:"",multiple:"true"==e.getAttribute("multipleSelect")},i=$(e).select2(t);if(t.multiple)if($(e).attr("seleteds")){var s=$(e).attr("seleteds").split(",");i.val(s).trigger("change")}else console.log("noseleteds"),i.val("");else i.val($(e).val()).trigger("change")}$(e).removeAttr("disabled")}function reHideField(e){$("#suivi_row_"+e).find("select, input").each((function(){doHideInput(this)})),$(".row-detail-"+e).each((function(e){doHideInput(this)}))}function showErrorIncador(e){$("#suivi_row_"+e).find("select, input").each((function(){if($(this).hasClass("form-select")){let e=$(this).parent("div");e.hasClass("is-invalid")||e.addClass("is-invalid")}else $(this).hasClass("is-invalid")||$(this).addClass("is-invalid")}))}function hideErrorIncador(e){$("#suivi_row_"+e).find("select, input").each((function(){if($(this).hasClass("form-select")){let e=$(this).parent("div");e.hasClass("is-invalid")&&e.removeClass("is-invalid")}else $(this).hasClass("is-invalid")&&$(this).removeClass("is-invalid")}))}function doHideInput(e){if($(e).hasClass("form-select")){if("select"==$(e).attr("data-control")||$(e).data("select2")){if(void 0!==$(e).attr("seleteds")&&!1!==$(e).attr("seleteds")){let t=$(e).val();t&&Array.isArray(t)&&($(e).attr("seleteds",t.join(",")),$(e).attr("multiple",!1))}$(e).data("select2")&&$(e).select2("destroy")}$(e).parent("div").removeClass("in-invalid"),$(e).addClass("form-select-transparent")}else $(e).removeClass("in-invalid"),$(e).addClass("form-control-transparent");$(e).attr("disabled",!0)}function save_item(e={},t){let i=$("#loading-item-"+e.item_id);i.css("display",""),t.css("display","none"),t.attr("disabled","true"),showLoadingTable(),$.ajax({url:url("/suivi/save/row"),type:"POST",dataType:"json",data:e,success:function(s){if(forceShowColomunsForEdit(),i.css("display","none"),t.attr("disabled","false"),s.success)reHideField(e.item_id),toastr.success(s.message),$("#save-item-"+s.row_id).css("display","none"),reloadTable();else{if(t.css("display",""),toastr.error(s.message),s.invalid_colones){let e=s.invalid_colones,t=s.validated_colones;e.forEach((function(e,t){if($("#input-"+e).hasClass("form-select")){$("#input-"+e).parent("div").addClass("is-invalid")}else $("#input-"+e).addClass("is-invalid")})),t.forEach((function(e,t){if($("#input-"+e).hasClass("form-select")){$("#input-"+e).parent("div").removeClass("is-invalid")}else $("#input-"+e).removeClass("is-invalid")}))}$("#save-item-"+s.row_id).css("display","none")}showLoadingTable(!1)},error:function(e,s,a){toastr.options.timeOut=0,toastr.error(a),showLoadingTable(!1),i.css("display","none"),t.css("display",""),t.attr("disabled","false")}})}function forceShowColomunsForEdit(){let e=hiddenedColoneUser.length;if(e)for(let t=0;t<e;t++){dataTableInstance.suiviTable.column(hiddenedColoneUser[t]).visible(!0)}}function forceHideColomunsForEdit(){let e=hiddenedColoneUser.length;if(e)for(let t=0;t<e;t++){dataTableInstance.suiviTable.column(hiddenedColoneUser[t]).visible(!1)}}function optionFormat(e){if(!e.id)return e.text;var t=document.createElement("span"),i="";return i+='<div class="d-flex align-items-center">',e.img&&(i+='<img src="'+e.img+'" class="rounded-circle h-40px me-2" alt="'+e.text+'"/>'),i+='<div class="d-flex flex-column">',i+='<span class=" fw-bold lh-1 text-info">'+e.text+"</span>",e.info&&(i+=' <span class="text-muted">'+e.info+"</span>"),i+="</div>",i+="</div>",t.innerHTML=i,$(t)}slider.addEventListener("mousemove",move,!1),slider.addEventListener("mousedown",startDragging,!1),slider.addEventListener("mouseup",stopDragging,!1),slider.addEventListener("mouseleave",stopDragging,!1),slider.addEventListener("mouseclick",stopDragging,!1),$(".suiviTable").on("change , keyup",(function(){dataTableInstance.suiviTable.ajax.reload()})),$("#do-search-suivi").on("click",(function(e){forceHideColomunsForEdit(),reloadTable()})),$("#search_suivi").on("keyup",(function(){dataTableInstance.suiviTable.search(this.value).draw()})),$(document).on("click",".edit-item",(function(e){$(this).attr("data-id");edit_row(this,!1)})),$(document).on("click",".cancel-edit-item",(function(e){var t=$(this).attr("data-id");dataTableInstance.suiviTable.draw(),$("#edit-item-"+t).css("display",""),$("#cancel-item-"+t).css("display","none")})),$(document).on("dblclick",".edit-item-part",(function(e){"true"==$(this).attr("data-can-edit")&&doEditInput(this);var t=$(this).attr("data-id");$("#save-item-"+t).css("display","")})),$(document).on("click",".clone-row",(function(e){var t=$(this).attr("data-suivi-item-id");$("#suivi_item_id").val(t),$("#submit-clone-row").trigger("click"),setTimeout((()=>{$("#suivi_item_id").val(0)}),1500)})),$(document).on("click",".save-item",(function(e){let t=$(this),i=$(this).attr("data-id"),s=$(this).attr("data-clone-of");var a={_token:_token,item_id:i,clon_of:s};if($("#suivi_row_"+i).find("select, input").each((function(){let e=$(this);if(e.attr("seleteds")&&"select"==e.attr("data-control")){let t=null;t=e.data("select2")?e.val():e.attr("seleteds").split(","),a[e.attr("name")]=t}else{let t=e.attr("name");"types[]"==t?(console.log("ewa"),a.types=e.val(),console.log($(this).val())):a[t]=e.val()}})),$(".row-detail-"+i).each((function(){a[$(this).attr("name")]=$(this).val()})),a.status_id==finishedId||a.status_id==pausedId){let e=a.item_id;return $.confirm({title:"Confirmation",content:"Voulez-vous vraimment continuer  ?",buttons:{"oui , je confirme !":function(){save_item(a,t)},nom:function(){return reHideField(e),$("#save-item-"+e).css("display","none"),$("#suivi_row_"+e).removeClass("bg-secondary"),!0}}})}save_item(a,t)})),$("#new-record-suivi").appForm({submitBtn:"#submit-clone-row",beforeAjaxSubmit:function(e,t,i){forceShowColomunsForEdit(),showLoadingTable()},onSuccess:function(e){dataTableaddRowIntheTop(dataTableInstance.suiviTable,e.item),showLoadingTable(!1),$("#folder_id").val("0").change(),e.is_clone&&(setTimeout((()=>{$("#edit-item-"+e.row_id).trigger("click")}),500),setTimeout((()=>{$("#suivi_row_"+e.row_id).addClass(getClassToEditColor(e.item.id))}),500))},onError:function(e){showLoadingTable(!1)},onFail:function(e,t,i){showLoadingTable(!1)}}),$("#folder_id").on("change",(function(){let e=$(this).html();"0"!=$(this).val()?$("#indicator-label").html('<i class="fas fa-clone"></i>'):$("#indicator-label").html(e)})),$(document).on("select2:unselect",".select2-dropdown",(function(e){e.params.originalEvent.stopPropagation()}));
        })
    </script>
@endsection
