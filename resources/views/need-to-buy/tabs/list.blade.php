<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body py-5">
                <div class="d-flex justify-content-end mb-5">
                    {{-- <button class="btn btn-sm btn-light-success" style="text-align: right">+ Nouvel besoin</button> --}}
                    @php
                        echo modal_anchor(url('/needToBuy/form-modal/'), '<button class="btn btn-sm btn-light-success" style="text-align: right">+ Nouvel besoin</button>', ['title' => "Ajout besoin"]);
                    @endphp
                </div>
                <div class="d-flex justify-content-end my-5">
                    <div class="filter-datatable">
                        @include('filters.filters-basic', [
                            'inputs' => $basic_filter,
                            'filter_for' => 'needToBuy',
                        ])
                    </div>
                    <div class="me-4 my-2">
                        <a id="do-search-project" title="Recharger"
                            class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                            <i class="fas fa-sync-alt" style="width: 10px;"></i>
                        </a>
                    </div>
                </div>
                <table id="needToBuy"
                    class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover"></table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        dataTableInstance.needToBuy = $("#needToBuy").DataTable({
            processing: true,
            dom: "tpr",
            columns: [{
                    data: "num_ticket",
                    title: "Ticket",
                    class: "text-primary"
                },
                {
                    data: "name",
                    title: 'Article'
                },
                {
                    data: "quantity",
                    title: 'Demandé'
                },
                {
                    data: "unit",
                    title: 'Unité'
                },
                {
                    data: "unit_price",
                    title: 'PU'
                },
                {
                    data: "total_price",
                    title: 'Prix Total'
                },
                {
                    data: "department",
                    title: "Departement"
                },
                {
                    data: "status",
                    title: "Statut"
                },
                {
                    data: "actions",
                    searchable: false,
                    orderable: false
                },
            ],
            ajax: {
                url: url("/needToBuy/data"),
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
            dataTableInstance.needToBuy.ajax.reload();
        });

        $('.needToBuy').on('change', function() {
            dataTableInstance.needToBuy.ajax.reload();
        });

        $(document).on("dblclick", ".editable", function() {
            var _this = $(this);
            let id = _this.attr("data-id");
            _this.attr("disabled", false);
            _this.removeClass("form-control-transparent");
            $("#action-" + id).css("display", "");
        });

        $(document).on("click", ".validate-need", function() {
            var _this = $(this);
            var id = _this.attr("data-action-id");
            var qty = $("#input-qty-" + id).val();
            $.ajax({
                type: "POST",
                url: url("/dd"),
                data: {
                    _token: _token,
                    id: id,
                    qty: qty
                },
                success: function(response) {



                }
            });
        });
    });
</script>
