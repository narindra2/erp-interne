<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body py-5">
                <div class="d-flex justify-content-end mb-5">
                    <div class="filter-datatable">
                        @include('filters.filters-basic', [
                            'inputs' => $basic_filter,
                            'filter_for' => 'needToBuyStat',
                        ])
                    </div>
                    <div class="me-4 my-2">
                        <a id="do-search-project" title="Recharger"
                            class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                            <i class="fas fa-sync-alt" style="width: 10px;"></i>
                        </a>
                    </div>
                </div>
                {{-- <div class="mb-5 text-center fs-3">Prix Total: <span id="totalPrice">0</span></div> --}}
                <div class="mb-5 text-center fs-3">Prix Total: <span id="totalPrice2">0</span></div>
                <table id="needToBuyStat" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover"></table>
                <div class="d-flex justify-content-center mt-5">
                    <button class="btn btn-sm btn-light-success" id="export">Generer un PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        KTApp.initSelect2();

        var calcul = function(settings, json) {
            $("#totalPrice").text(json.sum + ' Ar');
        }

        dataTableInstance.needToBuyStat = $("#needToBuyStat").DataTable({
            processing: true,
            dom: "tpr",
            columns: [
                {
                    data: "name",
                    title: 'Article'
                },
                {
                    data: "quantity",
                    title: 'à acheter'
                },
                {
                    data: "unit_price",
                    title: 'Prix Unitaire'
                },
                {
                    data: "total_price",
                    title: 'Prix Total'
                }
            ],
            ajax: {
                url: url("/need-to-buy/statistic-data"),
                data: function(data) {
                    <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                    data.{{ $input }} = $("#{{ $input }}").val();
                    <?php } ?>
                }
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
            initComplete: calcul,
           footerCallback: function ( row, data, start, end, display ) {
            var api = this.api();
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            // Total over all pages
            var total = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            $("#totalPrice2").text(total+ ' Ar');
        }
        });

        $('#do-search-project').on('click', function(e) {
            dataTableInstance.needToBuyStat.ajax.reload();
        });

        $('.needToBuyStat').on('change', function() {
            dataTableInstance.needToBuyStat.ajax.reload();
            $("#totalPrice").text("...");
            setTimeout(() => {
                let json = dataTableInstance.needToBuyStat.ajax.json();
                calcul(null, json);
            }, 2000);
        });

        $(document).on("dblclick", ".editable", function() {
            var _this = $(this);
            let id = _this.attr("data-id");
            _this.attr("disabled", false);
            _this.removeClass("form-control-transparent");
            $("#action-" + id).css("display", "");
        });

        $(document).on("click", "#export", function() {
            let urlPDF = url('/need-to-buy/pdf?');
            urlPDF += "status=" + $("#status").val();
            urlPDF += "&date=" + $("#date").val();
            console.log(urlPDF);
            window.open(urlPDF, '_blank').focus();
        });
    });
</script>
