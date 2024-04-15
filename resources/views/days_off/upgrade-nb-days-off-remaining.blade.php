<x-base-layout>

    <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10 " style="background-size: auto calc(100% + 10rem); background-position-x: 100%">
        <!--begin::Card header-->
        <div class="card-header pt-4 mb-4">
            <div class="d-flex align-items-center">
                <!--begin::Icon-->
                <div class="symbol symbol-circle me-5">
                    <div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs020.svg-->
                        <span class="svg-icon svg-icon-2x svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M17.302 11.35L12.002 20.55H21.202C21.802 20.55 22.202 19.85 21.902 19.35L17.302 11.35Z" fill="black"></path>
                                <path opacity="0.3" d="M12.002 20.55H2.802C2.202 20.55 1.80202 19.85 2.10202 19.35L6.70203 11.45L12.002 20.55ZM11.302 3.45L6.70203 11.35H17.302L12.702 3.45C12.402 2.85 11.602 2.85 11.302 3.45Z" fill="black"></path>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                </div>
                <!--end::Icon-->
                <!--begin::Title-->
                <div class="d-flex flex-column">
                    <h2 class="mb-1">Mis à jour des soldes de congés des employés</h2>
                    
                </div>
                <!--end::Title-->
            </div>
        </div>
        <!--end::Card header-->
    </div>

    <div class="card card-flush shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Liste des demandes à valider</h3>
        </div>
        <div class="card-body py-5">
            <div class="d-flex justify-content-end">
                <form action="{{ url('/days-off/upgrade') }}" id="modal-form" method="POST">
                    @csrf
                    <div class="row">
                        <label class="col-3 col-form-label">Quantité de jours à ajouter</label>
                        <div class="col-3">
                            <input type="number" name="nb" placeholder="2.5" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-solid form-control-sm">
                        </div>
                        <div class="col-4">
                            <button id="submit" class="btn btn-sm btn-light-success">Mettre à jour</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="">
                <table class="table table-row-dashed table-row-gray-300 gy-8 align-middle" id="tableUpgrade"></table>
            </div>
        </div>
    </div>
    @section('scripts')

    <script type="text/javascript">
        $(document).ready(function() {

            $("#modal-form").appForm({
                onSuccess: function(response) {
                    dataTableInstance.tableUpgrade.ajax.reload();
                },
            });

            dataTableInstance.tableUpgrade = $("#tableUpgrade").DataTable({
                processing: true,
                dom: "ltrpi",
                ajax: {
                    url: url("/days-off/upgrade/dataList"),
                    data: function(data) {
                        <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                            data.{{ $input }} = $("#{{ $input }}").val();
                        <?php } ?>
                        }
                },
                columns: [
                    {data: "registration_number", title: 'Matricule'},
                    {data: "name", title: 'Nom'},
                    {data: "job", title: "Poste"},
                    {data: "nb_days_off", title: "Solde de congé"},
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                },
            });

            $(".tableUpgrade").on("change", function() {
                dataTableInstance.tableUpgrade.ajax.reload();
            });
        });
    </script>
    @endsection
</x-base-layout>