<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body py-5">
                <div class="d-flex justify-content-end mb-5">
                    {{-- <div class="filter-datatable">
                        @include('filters.filters-basic', [
                            'inputs' => $basic_filter,
                            'filter_for' => 'needToBuy',
                        ])
                    </div> --}}
                    <div class="me-4 my-2">
                        <a id="do-search-project" title="Recharger"
                            class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                            <i class="fas fa-sync-alt" style="width: 10px;"></i>
                        </a>
                    </div>
                </div>
                <table id="invoice"
                    class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover"></table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        dataTableInstance.invoice = $("#invoice").DataTable({
            processing: true,
            dom: "tpr",
            columns: [{
                    data: "name",
                    title: "Nom",
                    class: "text-primary"
                },
                {
                    data: "amount",
                    title: 'Prix total'
                },
                {
                    data: "created_at",
                    title: 'Créé le'
                },
            ],
            ajax: {
                url: url("/need-to-buy/file-page/data"),
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

        $('#do-search-project').on('click', function(e) {
            dataTableInstance.invoice.ajax.reload();
        });

        $('.invoice').on('change', function() {
            dataTableInstance.invoice.ajax.reload();
        });
    });
</script>
