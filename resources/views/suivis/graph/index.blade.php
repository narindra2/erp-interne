<x-base-layout>
    <div id="" class="w-200">
        <div class="card shadow-sm  mb-3 ">
            <div class="card-header border-1 pt-1">
                <div class="me-2 card-title align-items-start ">
                    <span class="card-label  fs-3 mb-1 mt-3"> @lang('lang.suivi_stat') </span>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end mb-5">
                            <select data-hide-search="true" class="form-select form-select-solid form-select-sm w-200px" name="stat_on" id="stat_on"  data-control="select2" aria-label="Choisi un ....">
                                <option value="0" disabled selected>Stat versions par rapport</option>
                                <option value="Status|status_id">Status</option>
                                <option value="Montage|montage">Montages</option>
                                <option value="Pole|poles">Pôles</option>
                            </select>
                    </div>
                </div>
              
            </div>
        </div>
        <div class="card shadow-sm  mb-3 ">
            <div class="card-header border-1 pt-1">
                <div class="me-2 card-title align-items-start ">
                    <span class="card-label  fs-3 mb-1 mt-3"> </span>
                    <div class="text-muted fs-7 fw-bold">
                        
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end mb-5">
                            @include('filters.filters-basic', [
                                'inputs' => $stat_filter,
                                'filter_for' => 'statFilter',
                            ])
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="loading-data"></div>
                <canvas id="stat" class="mh-400px"></canvas>
            </div>
        </div>
    </div>
   
    @section('scripts')
        <script>
            $(document).ready(function() {
                var ctx = document.getElementById('stat');
                var ChartSuivi = null;
                // Define fonts
                var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');
             
                function onLoading(show  = true ) {
                    let  loading = `<div class="d-flex justify-content-center">
                                    <div class="spinner-border text-primary " style="width: 3rem; height: 3rem;" role="status">
                                    </div>
                                </div>`
                    if (show) {
                        $("#loading-data").html(loading)
                    } else {
                        $("#loading-data").html("")
                    }
                }                    
                function load(dataPost) {
                    if (ChartSuivi != null) {
                        ChartSuivi.destroy();
                    }
                    $.ajax({
                        url: url("/load/stat"),
                        type: 'POST',
                        dataType: 'json',
                        data: dataPost,
                        success: function(result) {
                            const data = {
                                labels: result.labels,
                                datasets: result.dataset,
                            };
                            const config = {
                                type: 'bar',
                                data: data,
                                options: {
                                    plugins: {
                                        title: {
                                            display: false,
                                        }
                                    },
                                    responsive: true,
                                    interaction: {
                                        intersect: false,
                                    },
                                    scales: {
                                        y: {
                                            ticks: {
                                                stepSize: 1,
                                            }
                                        }
                                    }
                                },
                                defaults: {
                                    global: {
                                        defaultFont: fontFamily
                                    }
                                }
                            };
                            onLoading(false)
                            ChartSuivi = new Chart(ctx, config)
                        },
                        error: function(request, status, error) {
                            toastr.error("error")
                            onLoading(false)
                        }
                    });
                }
                $("#stat_on").on("change", function() {
                    onLoading()
                    let data = {"_token": _token ,"stat_on": $("#stat_on").val() }
                    <?php foreach(inputs_filter_datatable($stat_filter) as $input ) { ?>
                        data.{{ $input }} = $("#{{ $input }}").val();
                    <?php } ?>
                    load(data)
                })
                $(".statFilter").on("change", function() {
                    onLoading()
                    let data = {"_token": _token ,"stat_on": $("#stat_on").val() }
                    <?php foreach(inputs_filter_datatable($stat_filter) as $input ) { ?>
                        data.{{ $input }} = $("#{{ $input }}").val();
                    <?php } ?>
                    load(data)
                })

                load({"_token": _token})
            })
        </script>
    @endsection
</x-base-layout>
