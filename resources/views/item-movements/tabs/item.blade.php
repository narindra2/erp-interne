<div class="card shadow-sm">
    <div class="d-flex justify-content-end mx-5 my-5">
        <div class="filter-datatable">
            @include('filters.filters-basic', ["inputs" => $basic_filter, "filter_for" => "stock"])
        </div>
        &nbsp; &nbsp;
        <div class="me-4 my-2 ml-3">
            <div class="d-flex align-items-center position-relative my-1">
                <input type="text" id="search_day_off" autocomplete="off"
                    class="form-control form-control-solid form-select-sm w-200px ps-9 "
                    placeholder="{{ trans('lang.search') }}">
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="item" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover ">
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        dataTableInstance.item = $("#item").DataTable({
            processing: true,
            dom: "ltpr",
            columns: [{
                    data: "created_at",
                    title: 'Créé le'
                },
                {
                    data: "type",
                    title: 'Type'
                },
                {
                    data: "code",
                    title: 'Code'
                },
                {
                    data: "location",
                    title: 'Lieu'
                },
                {
                    data: "local",
                    title: 'Local'
                },
                {
                    data: "actions",
                    searchable: false,
                    orderable: false
                },
            ],
            ajax: {
                url: url("/item-movements/items/data"),
                data: function(data) {
                    <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                        data.{{ $input }} = $("#{{ $input }}").val();
                    <?php } ?>
                }
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });

        $(".item").on("change", function() {
            dataTableInstance.item.ajax.reload();
        });
    });
</script>
