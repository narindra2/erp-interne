<x-base-layout>
    <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10" style="background-size: auto calc(100% + 10rem); background-position-x: 100%">
        <!--begin::Card header-->
        <div class="card-header border-1 pt-1">
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
                            <h2 class="mb-1">Liste des déclarants</h2>
                        </div>
                        <!--end::Title-->
                    </div>
                </div>
            </div>
            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                <a href="{{ url('/cerfa/form/') }}" class="btn btn-sm btn-light-primary">+ Nouvel déclarant</a>
            </div>
        </div>

        <!--end::Card header-->
    </div>
    <div class="card card-flush shadow-sm">
        <div class="card-body py-5">
            {{-- <div class="d-flex justify-content-end">
                <div class="filter-datatable">
                    @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "employeeList"])
                </div>
                <div class="me-4 my-2">
                    <a id="do-reload-list" title = "Recharger" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                        <i class="fas fa-sync-alt" style="width: 10px;"></i>
                    </a>
                </div>
            </div> --}}
            <div class="">
                <table class=" table table-row-dashed table-row-gray-300 gy-4 align-middle" id="customerList"></table>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.customerList = $("#customerList").DataTable({
                    processing: true,
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                    ajax: {
                        url: url("/cerfa/customer/dataList"),
                    },
                    columns: [
                        {data: "customer_name", title: 'Nom'},
                        {data: "email", title: 'Email'},
                        {data: "type", title: 'Type'},
                        {data: "action", title: ''}
                    ],
                });
            })
        </script>
    @endsection

</x-base-layout>
