<style>
    tr < td-disabled {
    }
</style>
<div class="card shadow-sm">
    <div class="card-body">

        <div class="form-group row mb-10">
            <label for="" class="col-2 col-form-label">Date</label>
            <div class="col-2">
                <input type="date" class="form-control" id="date" value="{{ $purchase->purchase_date }}">
            </div>
        </div>
        <div class="form-group row mb-10">
            <label for="" class="col-2 col-form-label">Prix Total</label>
            <label for="" class="col-2 col-form-label" id="detailTotalPrice">0</label>
        </div>

        <div class="separator separator-dashed my-8"></div>

        <h2 class="text-center">Les articles</h2>

        <div class="d-flex justify-content-end my-4">
            <button class="btn btn-light-success font-weight-bold btn-sm"><i class="fas fa-plus"></i> Nouveau</button>
        </div>

        <table id="detail" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
        </table>
    </div>
</div>

<script>
    KTApp.initSelect2();
    $(document).ready(function () {
        $(document).ready(function () {
        dataTableInstance.purchaseList = $("#detail").DataTable({
            processing: true,
            dom: "ltpr",
            columns: [{
                    data: "item",
                    title: "Article"
                }, {
                    data: "quantity",
                    title: "Quantité"
                }, {
                    data: "unit_price",
                    title: "Prix Unitaire"
                },
                {
                    data: "total_price",
                    title: "Prix Total"
                },
                {
                    data: "actions",
                    searchable: false,
                    orderable: false
                }
            ],
            ajax: {
                url: url("/purchases/data_purchase_detail/1"),
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });
    });
    });
</script>