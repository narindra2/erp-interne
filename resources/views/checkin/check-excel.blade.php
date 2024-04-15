<x-base-layout>

    <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10" style="background-size: auto calc(100% + 10rem); background-position-x: 100%">
        <!--begin::Card header-->
        <!--begin::Card header-->
        <div class="card-header border-1 pt-1">
            <div class="me-2 card-title align-items-start flex-column">

                <div class="text-muted fs-7 fw-bold"> 
                    <div class="d-flex align-items-center">
                        <!--begin::Icon-->
                        <div class="symbol symbol-circle me-5">
                            <div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                                <i class="fas fa-fingerprint fs-3"></i>
                            </div>
                        </div>
                        <!--end::Icon-->
                        <!--begin::Title-->
                        <div class="d-flex flex-column">
                            <h2 class="mb-1">Pointage biométrique</h2>
                        </div>
                        <!--end::Title-->
                    </div>
                </div>
            </div>
            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
            <a href="#" class=""></a>
            @php
                echo modal_anchor(url("/users-pointing-excel/form-modal"), "+ Importer un fichier excel", ["title" => "Importation des données de pointage", "data-modal-lg" => true, "class"=> "btn btn-sm btn-light-primary"]);
            @endphp
            <a href="{{ url("/user-pointing/resume") }}" class="btn btn-sm btn-light-success font-weight-bold mr-2">Resume</a>
        </div>
        </div>
        <!--end::Card header-->
    </div>

    <div class="card card-flush shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Pointage Excel</h3>
        </div>
        <div class="card-body py-5">
            <div class="d-flex justify-content-end">
                <div class="filter-datatable">
                    @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "pointingList"])
                </div>
            </div>
            <div class="">
                <table class=" table table-row-dashed table-row-gray-300 gy-4 align-middle" id="pointingList"></table>
            </div>
        </div>
    </div>

    @section('scripts')

    <script type="text/javascript">
        $(document).ready(function() {

            dataTableInstance.pointingList = $("#pointingList").DataTable({
                processing: true,
                dom: "ltrpi",
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                },
                ajax: {
                    url: url("/users-pointing-excel/dataList"),
                    data: function(data) {
                        <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                            data.{{ $input }} = $("#{{ $input }}").val();
                        <?php } ?>
                    }
                },
                columns: [
                    {data: "date", title: "date"},
                    {data: "registration_number", title: "Matricule"},
                    {data: "entry_time", title: "Heure d'entrée"},
                    {data: "exit_time", title: "Heure sortie"},
                    {data: "time_works", title: "Temps travaillé"},
                    {data: "cumulative_hour", title: "Heure cumulé"}
                ],
            });

            $(".pointingList").on("change", function() {
                dataTableInstance.pointingList.ajax.reload();
            });
            
            
        });
    </script>

    @endsection
</x-base-layout>