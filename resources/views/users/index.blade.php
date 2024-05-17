<x-base-layout>

    <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10" style="background-size: auto calc(100% + 10rem); background-position-x: 100%">
        <!--begin::Card header-->
        <div class="card-header border-1 pt-4 mb-4">
            <div class="me-2 card-title align-items-start flex-column">

                <div class="text-muted fs-7 fw-bold">
                    <div class="d-flex align-items-center">
                        <!--begin::Icon-->
                        <div class="symbol symbol-circle me-5">
                            <div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <!--end::Icon-->
                        <!--begin::Title-->
                        <div class="d-flex flex-column">
                            <h2 class="mb-1">Gestion des employés</h2>
                        </div>
                        <!--end::Title-->
                    </div>
                </div>
            </div>
            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                <a href="{{ url('/user/form') }}" class="btn btn-sm btn-light-primary">+ Nouvel employé</a>
            </div>

        <!--end::Card header-->
    </div>

    <div class="card card-flush shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Liste des employés</h3>
        </div>
        <div class="card-body py-5">
            <div class="d-flex justify-content-end">
                <div class="filter-datatable">
                    @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "employeeList"])
                </div>
                <div class="me-4 my-2">
                    <a id="do-reload-list" title = "Recharger" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                        <i class="fas fa-sync-alt" style="width: 10px;"></i>
                    </a>
                </div>
            </div>
            <div class="">
                <table class=" table table-row-dashed table-row-gray-300 gy-4 align-middle" id="employeeList"></table>
            </div>
        </div>
    </div>

    @section('scripts')

    <script type="text/javascript">
        $(document).ready(function() {

            dataTableInstance.employeeList = $("#employeeList").DataTable({
                processing: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                },
                ajax: {
                    url: url("/user/dataList"),
                    data: function(data) {
                        <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                            data.{{ $input }} = $("#{{ $input }}").val();
                        <?php } ?>
                    }
                },
                columns: [
                    {data: "registration_number", title: 'Matricule' , class :'fw-bold fs-6 text-gray-800 border-bottom-2 border-gray-200'},
                    {data: "avatar", title: ''},
                    {data: "name", title: 'Nom'},
                    {data: "job", title: 'Poste'},
                    {data: "nb_days_off_remaining", title: 'Solde congé'},
                    {data: "nb_permission", title: 'Permission'},
                    // {data: "job", title: 'role'},
                    {data: "contract_type", title: "Contrat" ,class :'text-center'},
                    {data: "user_type", title: "Rôle"},
                    {data: "department", title: "Departement"},
                    {data: "local", title: "Local"},
                    {data: "detail", title: ""}
                ],
            });

            $(".employeeList").on("change", function() {
                dataTableInstance.employeeList.ajax.reload();
            });
            $('#do-reload-list').on('click', function(e) {
                    dataTableInstance.employeeList.ajax.reload();
            });
        });
    </script>

    @endsection
</x-base-layout>
