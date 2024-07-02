<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-end mb-0">
            <div class="filter-datatable">
                @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "inventorTable"])
            </div>
            &nbsp; &nbsp;
            <div class="me-4 my-2 ml-3">
                <div class="d-flex align-items-center position-relative my-1">
                    <input type="text" id="search-item" autocomplete="off"
                        class="form-control form-control-solid form-select-sm w-200px ps-9 "
                        placeholder="{{ trans('lang.search') }}">
                </div>
            </div>
            <div class="me-4 my-2">
                <a id="do-reload" title = "Recharger" class="btn btn-sm btn-outline  btn-outline-default">
                    <i class="fas fa-sync-alt" style="width: 10px;"></i>
                </a>
            </div>
            <div class="me-4 my-2">
                @php
                    echo modal_anchor(url('/stock/inventory/create-article'), '<button class="btn btn-sm btn-light-info "><i class="fas fa-plus "></i> Enreigistrement</button>', ['title' => 'Nouvel enreigistrement'])
                @endphp
            </div>
        </div>
        <table id="invetoryListDataTable" class="table table-row-dashed table-row-gray-200 align-middle table-hover ">
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        setTimeout(() => {
            KTApp.initSelect2();
        }, 1000);
        dataTableInstance.invetoryListDataTable = $("#invetoryListDataTable").DataTable({
            processing: true,
            paging: false,
            dom:"itpr",
            columns: [
                { data: "qrcode", orderable:false},
                { data: "code",title: 'Code' , orderable:false},
                { data: "name",title: 'Article'},
                { data: "propriety",title: 'Critère'},
                { data: "sub_cat",title: 'sous-cat'},
                { data: "cat",title: 'Categorie'},
                { data: "date",title: 'Date d\'aquisation'},
                { data: "num_invoice",title: 'N° facture'},
                { data: "prix_ht",title: 'Montant HT'},
                // { data: "prix_htt",title: 'Montant HTT'},
                { data: "etat",title: 'Etat'},
                { data: "observation",title: 'observation'},
                { data: "detail",title: ''},
            ],
            ajax: {
                url: url("/stock/inventory/data_list"),
                data: function(data) {
                    <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                        data.{{ $input }} = $("#{{ $input }}").val();
                    <?php } ?>
                }
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
            initComplete: function(settings, json) {
                setTimeout(() => {
                    KTApp.initBootstrapTooltips();
                }, 1000);
            }
        });

        $('.inventorTable').on('change', function() {
            dataTableInstance.invetoryListDataTable.ajax.reload();
            setTimeout(() => {
                KTApp.initBootstrapTooltips();
            }, 1000);
        });
        $('#search-item').on('keyup', function() {
            dataTableInstance.invetoryListDataTable.search(this.value).draw();
        });
        $('#do-reload').on('click', function(e) {
            dataTableInstance.invetoryListDataTable.ajax.reload();
            setTimeout(() => {
                KTApp.initBootstrapTooltips();
            }, 1000);
        });
        
    });
</script>
