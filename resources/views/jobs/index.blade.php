<x-base-layout>
    <div class="row">
        <div class="col-xxl-12 mb-10" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> @lang('lang.job_management') </span>
                        <div class="text-muted fs-7 fw-bold"></div>
                    </div>
                    <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                        data-bs-original-title="{{ trans('lang.new_job') }}">
                        @php
                            echo modal_anchor(url("/jobs/modal"), '<i class="fas fa-plus"></i>' . trans('lang.new_job'), ['title' => trans('lang.new_job'), 'class' => 'btn btn-sm btn-light-primary']);
                        @endphp
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-8" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-body py-5">
                    <table id="jobs" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.jobs = $("#jobs").DataTable({
                    processing: true,
                    columns: [ 
                        {data :"name" , title: 'Nom', "class":"text-left"},
                        {data :"actions"},
                    ],
                    ajax: {
                        url: url("/jobs/dataList"),
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                });

                $("#deleteJob").on("click", function(e) {
                    $.ajax({
                        type: "POST",
                        url: url("/job/delete/" + $(this).data("id")),
                        data: {
                            _token: _token
                        },
                        success: function (response) {
                            $("#ajax-modal").modal().hide();
                            dataTableInstance.jobs.ajax.reload();
                        }
                    });
                });
            });
        </script>
    @endsection
</x-base-layout>
