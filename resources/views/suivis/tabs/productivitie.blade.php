<div class="card shadow-sm mb-4 ">
    <div class="card-header border-0">
        <div class="card-toolbar">
            <div class="filter-datatable">
                @include('filters.filters-basic', ['inputs' => $basic_filter, 'filter_for' => 'prodTable'])
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div id="sectionTableColumnDinamic"></div>
        <div id="sectionTableDinamic"></div>
    </div>
</div>
@section('scripts')
    <script>
        $(document).ready(function() {
            var xhr = null;

            function loadDataTable() {
                let ramdonDatatableId = "prodTable-" + (Math.random() + 1).toString(36).substring(7);
                $("#sectionTableDinamic").html("");
                $("#sectionTableColumnDinamic").html("");
                let tableDimamicHtml =
                    `<div class="table-responsive">
                        <table id="${ramdonDatatableId}" class="table table-row-bordered gy-3 ">
                            <div id="loading-prod" class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </table>
                    </div>`;
                $("#sectionTableDinamic").html(tableDimamicHtml);
                if (dataTableInstance.prodTable) {
                    dataTableInstance.prodTable.destroy();
                    dataTableInstance.prodTable = null;
                }
                let postData = {};
                postData._token = _token;
                <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                    postData.{{ $input }} = $("#{{ $input }}").val();
                <?php } ?>
                if (xhr !== null && xhr.readyState != 4) {
                    xhr.abort();
                }
                xhr = $.ajax({
                    "url": url("/load/prod"),
                    "type": "POST",
                    "dataType": "json",
                    "data": postData,
                    "success": function(response) {
                        if (!response.success) {
                            return toastr.error(response.message);
                        }
                        let targetsColoredCell = []
                        let targetsColumns = "<span> <u>Colones </u> : </span>";
                        for (let index = 2; index < response.columns.length; index++) {
                            targetsColoredCell.push(index);
                            targetsColumns = targetsColumns + 
                                `[<a style="cursor:pointer;" class="toggle-vis" data-column="${index}">${response.columns[index].title}</a>]`;
                        }
                        $("#sectionTableColumnDinamic").html(targetsColumns);
                        dataTableInstance.prodTable = $('#' + ramdonDatatableId).DataTable({
                            dom: "frtp",
                            data: response.data,
                            processing: true,
                            ordering: false,
                            columns: response.columns,
                            columnDefs: [{
                                targets: targetsColoredCell,
                                createdCell: function(td, cellData, rowData, row, col) {
                                    if ((response.columns)[col].columnColor) {
                                        if (parseInt(cellData)) {
                                            $(td).css("background-color", response.columns[col].columnColor)
                                        }
                                    }
                                }
                            }]
                        });
                        $('#loading-prod').remove();
                    },
                });
            }
            loadDataTable();
            $('.prodTable').on('change', function() {
                loadDataTable();
            });
            $(document).on('click','a.toggle-vis', function(e) {
                e.preventDefault();
                var column = dataTableInstance.prodTable.column($(this).attr('data-column'));
                column.visible() ?  $(this).addClass("text-gray-500") : $(this).removeClass("text-gray-500");
                column.visible(!column.visible());
            });
        })
    </script>
@endsection
