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
                $hiddened = App\Models\SuiviColumnCustomed::get_user_hidden_columns_array();
            @endphp
            Colonnes : 
            @foreach ($model::$TABLE_ALLOWED_COLUMNS as $key => $columns)
                @if (!in_array($key,$model::$NOT_CUSTOMABLE_COLUMNS))
                    <a class="columns-visibility {{ in_array($key,$hiddened) ? "text-gray-500" : "" }}" data-column="{{ $key }}">{{ $columns }}</a> @if(!$loop->last)  -  @endif
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
        })
        .DataTable({
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
        
        function showLoadingTable(show = true) {
            if (show) {
            //    blockUITableDiv.block();
                 $("#suiviTable_processing").css("display", "")
            } else {
            //   blockUITableDiv.release();
                $("#suiviTable_processing").css("display", "none")
            }
        } 
        /** Move table */ 
        let mouseDown = false;
        let startX, scrollLeft;
        const slider = document.querySelector('.table-responsive');

        const startDragging = (e) => {
        mouseDown = true;
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
        }

        const stopDragging = (e) => {
        mouseDown = false;
        }

        const move = (e) => {
        e.preventDefault();
        if(!mouseDown) { return; }
        const x = e.pageX - slider.offsetLeft;
        const scroll = x - startX;
        slider.scrollLeft = scrollLeft - scroll;
        }

        // Add the event listeners
        slider.addEventListener('mousemove', move, false);
        slider.addEventListener('mousedown', startDragging, false);
        slider.addEventListener('mouseup', stopDragging, false);
        slider.addEventListener('mouseleave', stopDragging, false);
        slider.addEventListener('mouseclick', stopDragging, false);

        $('.suiviTable').on('change , keyup', function() {
            dataTableInstance.suiviTable.ajax.reload();
        });
        $('#do-search-suivi').on('click', function(e) {
            forceHideColomunsForEdit()
            reloadTable()
        });
        
        function reloadTable() {
            if (loopMinutors) {
                for (var item in loopMinutors) {
                    clearInterval(loopMinutors[item])
                }
            }
            dataTableInstance.suiviTable.ajax.reload();  
        }
        $('#search_suivi').on('keyup', function() {
            dataTableInstance.suiviTable.search(this.value).draw();
        });
        /** Edit item */ 
        $(document).on('click', ".edit-item", function(e) {
            var itemId = $(this).attr("data-id");
            edit_row(this, false)
            // $("#edit-item-" + itemId).css("display", "none")
            // $("#cancel-item-" + itemId).css("display", "")
        });
         /** Cancel edit item */ 
        $(document).on('click', ".cancel-edit-item", function(e) {
            var itemId = $(this).attr("data-id");
            dataTableInstance.suiviTable.draw();
            $("#edit-item-" + itemId).css("display", "")
            $("#cancel-item-" + itemId).css("display", "none")
        });
        
      
        
        $(document).on('dblclick', ".edit-item-part", function(e) {
            if ($(this).attr("data-can-edit") == "true") {
                doEditInput(this)
            }
            var itemId = $(this).attr("data-id");
            $("#save-item-" + itemId).css("display", "")
        });

        function edit_row(_this, forceEdit = false) {
            forceShowColomunsForEdit();
            var itemId = $(_this).attr("data-id");
            $('#suivi_row_' + itemId).find("select, input").each(function(e) {
                if ($(this).attr("data-can-edit") == "true" || forceEdit) {
                    doEditInput(this)
                }
            });
            $('.row-detail-' + itemId).each(function(e) {
                if ($(this).attr("data-can-edit") == "true" || forceEdit) {
                    doEditInput(this)
                }
            });
            $("#save-item-" + itemId).css("display", "")
            $("#suivi_row_" + itemId).addClass(getClassToEditColor(itemId));
             
        }
        function getClassToEditColor(itemId) {
            let color =  $("#class-row-color-"+ itemId).data("row-color"); // in this item status_hidden column div
            // return color ? color : backgroundRowEdit;
            return  backgroundRowEdit;
        }
        function doEditInput(element) {
            if ($(element).hasClass("form-control")) {
                $(element).removeClass("form-control-transparent")
            }
            if ($(element).hasClass("form-select")) {
                $(element).removeClass("form-select-transparent")

                var options = {
                    "templateResult": optionFormat,
                    "dir": document.body.getAttribute('direction'),
                    "minimumResultsForSearch": element.getAttribute('data-hide-search') == 'true' ?  Infinity : "",
                    "multiple": element.getAttribute('multipleSelect') == 'true' ? true : false,
                };
                var selecte2 = $(element).select2(options);
                if (options.multiple) {
                    if ($(element).attr("seleteds")) {
                        var seletedsMultiple = $(element).attr("seleteds").split(",");
                        selecte2.val(seletedsMultiple).trigger('change')
                    } else {
                        console.log("noseleteds");
                        selecte2.val("")
                    }
                }else{
                    selecte2.val($(element).val()).trigger('change')
                }
            }
            $(element).removeAttr("disabled");
        }

      
        $(document).on('click', ".clone-row", function(e) {
            var itemId = $(this).attr("data-suivi-item-id");
            $("#suivi_item_id").val(itemId);
            $("#submit-clone-row").trigger("click");
            setTimeout(() => {
                $("#suivi_item_id").val(0);
            }, 1500);
        });

        $(document).on('click', ".save-item", function(e) {
            let _saveBtn = $(this);
            let itemId = $(this).attr("data-id");
            let clon_of = $(this).attr("data-clone-of");
            var fields = {
                "_token": _token,
                "item_id": itemId,
                "clon_of": clon_of
            }
            $('#suivi_row_' + itemId).find("select, input").each(function() {
                let _this2 = $(this);
                if (_this2.attr("seleteds") && _this2.attr("data-control") == "select") {
                    let vals = null
                    if (_this2.data('select2')) {
                        vals =_this2.val();
                    } else {
                        vals = _this2.attr("seleteds").split(",")
                    }
                    fields[_this2.attr("name")] = vals
                } else {
                    let name = _this2.attr("name");
                    if (name =="types[]") {
                        console.log("ewa");
                        fields["types"] = _this2.val()
                        console.log($(this).val());
                    }else{
                        fields[name] = _this2.val()
                    }
                }
            });

            $('.row-detail-' + itemId).each(function() {
                fields[$(this).attr("name")] = $(this).val()
            });
            if (fields.status_id ==  finishedId || fields.status_id == pausedId) {
                let itemId = fields.item_id;
                return  $.confirm({
                    title: 'Confirmation',
                    content: `Voulez-vous vraimment continuer  ?`,
                    buttons: {
                        "oui , je confirme !": function () {
                            save_item(fields,_saveBtn);
                        },
                        nom: function () {
                            reHideField(itemId)
                            $("#save-item-" + itemId).css("display", "none")
                            $("#suivi_row_"+ itemId).removeClass("bg-secondary");
                            return true;
                        },
                    }
                });
                return;
            }
            save_item(fields,_saveBtn);
        });
        function reHideField(itemId) {
            $('#suivi_row_' + itemId).find("select, input").each(function() {
                doHideInput(this)
            });
            $('.row-detail-' + itemId).each(function(e) {
                doHideInput(this)
            });
        }

        function showErrorIncador(itemId) {
            $('#suivi_row_' + itemId).find("select, input").each(function() {
                if ($(this).hasClass("form-select")) {
                    let div = $(this).parent("div");
                    if (!div.hasClass("is-invalid")) {
                        div.addClass("is-invalid");
                    }
                } else {
                    if (!$(this).hasClass("is-invalid")) {
                        $(this).addClass("is-invalid");
                    }
                }
            });
        }

        function hideErrorIncador(itemId) {
            $('#suivi_row_' + itemId).find("select, input").each(function() {
                if ($(this).hasClass("form-select")) {
                    let div = $(this).parent("div");
                    if (div.hasClass("is-invalid")) {
                        div.removeClass("is-invalid");
                    }
                } else {
                    if ($(this).hasClass("is-invalid")) {
                        $(this).removeClass("is-invalid");
                    }
                }
            });
        }
        function doHideInput(el) {
            if ($(el).hasClass("form-select")) {
                if ($(el).attr('data-control') == "select" || $(el).data('select2') ) {
                    if (typeof $(el).attr("seleteds") !== 'undefined' && $(el).attr("seleteds") !== false) {
                        let vals = $(el).val();
                        if (vals  &&  Array.isArray(vals)) {
                            $(el).attr("seleteds", vals.join(","))
                            $(el).attr("multiple", false)
                        }
                    }
                    if ($(el).data('select2')) {
                        $(el).select2('destroy');
                    }
                }
                $(el).parent("div").removeClass("in-invalid");
                $(el).addClass("form-select-transparent")
            } else {
              
                $(el).removeClass("in-invalid");
                $(el).addClass("form-control-transparent")
            }
            $(el).attr("disabled", true)
        }

        function save_item(data = {},_saveBtn) {
            let _loading =  $("#loading-item-" + data.item_id)
            _loading.css("display", "")
            _saveBtn.css("display", "none");
            _saveBtn.attr("disabled", "true");
            showLoadingTable()
            $.ajax({
                url: url("/suivi/save/row"),
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(result) {
                    forceShowColomunsForEdit();
                    _loading.css("display", "none")
                    _saveBtn.attr("disabled", "false");
                    if (result.success) {
                        reHideField(data.item_id)
                        toastr.success(result.message);
                        $("#save-item-" + result.row_id).css("display", "none")
                        reloadTable();

                    } else {
                        _saveBtn.css("display", "");
                        toastr.error(result.message);
                        if (result.invalid_colones) {
                            let invalid_colones = result.invalid_colones
                            let validated_colones = result.validated_colones
                            invalid_colones.forEach(function(input, index) {
                                if ($("#input-" + input).hasClass("form-select")) {
                                    let div = $("#input-" + input).parent("div");
                                    div.addClass("is-invalid");
                                } else {
                                    $("#input-" + input).addClass("is-invalid");
                                }
                            });
                            validated_colones.forEach(function(input, index) {
                                if ($("#input-" + input).hasClass("form-select")) {
                                    let div = $("#input-" + input).parent("div");
                                    div.removeClass("is-invalid");
                                } else {
                                    $("#input-" + input).removeClass("is-invalid");
                                }
                            });
                        }
                        $("#save-item-" + result.row_id).css("display", "none")
                    }
                    showLoadingTable(false)
                },
                error: function(request, status, error) {
                    toastr.options.timeOut = 0;
                    toastr.error(error);
                    showLoadingTable(false)
                    _loading.css("display", "none")
                    _saveBtn.css("display", "");
                    _saveBtn.attr("disabled", "false");
                }
            });
        }
        /** Show all column to edit or to fill*/
        function forceShowColomunsForEdit() {
            let countCol = hiddenedColoneUser.length
            if (countCol) {
                for (let i = 0; i < countCol; i++) {
                    let column = dataTableInstance.suiviTable.column(hiddenedColoneUser[i]);
                    column.visible(true);
                }
            }
            
        }
          /** Rehide all column to edit or to fill*/
        function forceHideColomunsForEdit() {
            let countCol = hiddenedColoneUser.length
            if (countCol) {
                for (let i = 0; i < countCol; i++) {
                    let column = dataTableInstance.suiviTable.column(hiddenedColoneUser[i]);
                    column.visible(false);
                }
            }
        }
        $("#new-record-suivi").appForm({
            submitBtn: "#submit-clone-row",
            beforeAjaxSubmit: function(data, self, options) {
                forceShowColomunsForEdit();
                showLoadingTable()
            },
            onSuccess: function(response) {
                dataTableaddRowIntheTop(dataTableInstance.suiviTable, response.item)
                showLoadingTable(false)
                $('#folder_id').val("0").change();
                if (response.is_clone) {
                    setTimeout(() => {
                        $("#edit-item-" + response.row_id).trigger("click");
                    }, 500);
                    setTimeout(() => {
                        $("#suivi_row_" + response.row_id).addClass(getClassToEditColor(response.item.id))
                    }, 500);
                }
                return;
            },
            onError: function(response) {
                showLoadingTable(false)
                return;
            },
            onFail: function(request, status, error) {
                showLoadingTable(false)
                return;
            }
        })
        $('#folder_id').on("change", function() {
            let html = $(this).html()
            if ($(this).val() != "0") {
                $("#indicator-label").html('<i class="fas fa-clone"></i>')
            } else {
                $("#indicator-label").html(html)
            }
        });

        function optionFormat(item) {
            if (!item.id) {
                return item.text;
            }
            var span = document.createElement('span');
            var template = '';
            template += '<div class="d-flex align-items-center">';
            if (item.img) {
                template += '<img src="' + item.img + '" class="rounded-circle h-40px me-2" alt="' + item.text +
                    '"/>';
            }
            template += '<div class="d-flex flex-column">'
            template += '<span class=" fw-bold lh-1 text-info">' + item.text + '</span>';
            if (item.info) {
                template += ' <span class="text-muted">' +  item.info + '</span>';
            }
            template += '</div>';
            template += '</div>';
            span.innerHTML = template;
            return $(span);
        }
       // Bug : empêcher la fermeture du dropdown pour chaque select2 dans dropdown menu  filtre avancé
        $(document).on('select2:unselect', '.select2-dropdown', function(e) {
            e.params.originalEvent.stopPropagation();
        });
    })
</script>
@endsection
