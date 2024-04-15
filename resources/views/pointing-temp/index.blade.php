<x-base-layout>
    <div class="row">
        <div class="col-xxl-12 mb-10" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> Gestion pointage temporaire </span>
                        <div class="text-muted fs-7 fw-bold"></div>
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
                    </div>
                    <table id="pointing_temp" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.pointing_temp = $("#pointing_temp").DataTable({
                    processing: true,
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
            })
        </script>
    @endsection
</x-base-layout>
