<x-base-layout>
    <div class="row">
        <div class="col-xxl-12 mb-2" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> Gestion pointage temporaire </span>
                        <div class="text-muted fs-7 fw-bold"></div>
                    </div>
                    <div class="card-toolbar accordion-header">
                    <button id="" class="accordion-button btn-sm"  style="background-color: white !important" type="button" data-bs-toggle="collapse" data-bs-target="#import-section" aria-expanded="true" aria-controls="kt_accordion_1_body_1">
                       Faire de l'importation .csv &nbsp; &nbsp;
                    </button>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 0">
                <div class="accordion" id="kt_accordion_1">
                    <div class="accordion-item">
                        <div id="import-section" class="accordion-collapse collapse " aria-labelledby="import-section" data-bs-parent="#kt_accordion_1">
                            <div class="accordion-body">
                                <form id="import-form" method="POST" action="{{ url('/pointing-temp/import-file') }}" >
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-7 ">
                                            <label for="file" class="mb-1">Choississez le fichier de pointage (.csv) : </label>
                                            <input type="file" name="file"  accept=".csv"  class="form-control form-control-sm" placeholder="name@example.com"/>
                                        </div>
                                        <div class="col-md-5 mt-6">
                                            <button type="submit" id="do-import" class=" btn btn-sm btn-light-primary"
                                                title="Faire l'importation">
                                                @include('partials.general._button-indicator', [
                                                    'label' => 'Importer maintenant',
                                                    'message' => trans('lang.sending'),
                                                ])
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
        <div class="col-xxl-12" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-body py-5">
                    <div class="d-flex justify-content-end mb-5">
                        <div class="filter-datatable">
                            @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "pointing_temp"])
                        </div>
                        &nbsp; &nbsp;
                        <div class="me-4 my-2 ml-3">
                            <div class="d-flex align-items-center position-relative my-1">
                                <input type="text" id="search" autocomplete="off"
                                    class="form-control form-control-solid form-select-sm w-300px ps-9 "
                                    placeholder="{{ trans('lang.search') }}">
                            </div>
                        </div>
                    </div>
                    <table id="pointing_temp" class="table mt-2 table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.pointing_temp = $("#pointing_temp").DataTable({
                    processing: true,
                    dom : "trp",
                    columns: [ 
                        {data :"registration_number" , title: 'Matricule'},
                        {data :"name" , title: "Nom"},
                        {data: "input", title: "Heure cumulé"},
                        {data: "actions"}
                    ],
                    ajax: {
                        url: url("/pointing-temp/data"),
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

                $(".pointing_temp").on("change", function() {
                    dataTableInstance.pointing_temp.ajax.reload();
                });
                $('#search').on('keyup', function() {
                    dataTableInstance.pointing_temp.search(this.value).draw();
                });
                $(document).on("click", ".save-pointing", function(e) {
                    e.preventDefault();
                    let userID = $(this).data('user_id');
                    let minuteWorked = $("#" + userID).val();
                    if (minuteWorked == "") toastr.error("Veuillez remplir l'heure cumulé");
                    $.ajax({
                        type: "POST",
                        url: url("/pointing-temp"),
                        data: {
                            _token: _token,
                            user_id: userID,
                            minute_worked: minuteWorked
                        },
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                });
                $("#import-form").appForm({
                isModal:true,
                submitBtn : "#do-import",
                showProgress : true,
                onSuccess: function(response) {
                    if (response.success) {
                        dataTableInstance.pointing_temp.ajax.reload();
                        setTimeout(() => {
                            $(".accordion-button").trigger("click")
                        }, 1000);
                    }
                },
                onError: function(response) {
                   
                },
            })
            })
        </script>
    @endsection
</x-base-layout>
