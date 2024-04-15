<x-base-layout>
    <div class="row">
        <div class="col-md-8">
            <div class="col-xxl-12 mb-5" >
                <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                    <div class="card-header border-1 pt-1">
                        <div class="me-2 card-title align-items-start flex-column">
                            <span class="card-label  fs-3 mb-1"> @lang('lang.list_dayoff_and_permission') </span>
                            <div class="text-muted fs-7 fw-bold"></div>
                        </div>
                        <div class="card-toolbar"  >
                            @php
                                echo modal_anchor(url("/days-off/daysOffType/modal_form"), '<i class="fas fa-plus"></i>' . trans('lang.add_new_type') . " ou permission" , ['title' => trans('lang.add_new_type') . " ou permission" , 'class' => 'btn btn-sm btn-light-primary']);
                            @endphp
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-12" >
                <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                    <div class="card-body py-5">
                        <table id="daysOffType" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @include('days_off.dayoff-nature-color.index')
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.daysOffType = $("#daysOffType").DataTable({
                    dom: "tp",
                    processing: true,
                    ordering: false,
                    columns: [ 
                        {data :"name" , title: 'Congé',"class":"text-left"},
                        {data :"description" , title: 'description',"class":"text-left"},
                        {data :"type" , title: 'Type de congé',"class":"text-left"},
                        {data :"nb_days" , title: 'Nombre de jour',"class":"text-left"},
                        {data :"impact_in_dayoff_balance" , title: 'Impacte sur solde congé',"class":"text-left"},
                        {data :"enable" , title: 'Status',"class":"text-left"},
                        {data :"actions" , title: '<i class="fas fa-bars" style="font-size:20px"></i>',"class":"text-left"},
                    ],
                    ajax: {
                        url: url("/days-off/type-data-list"),
                    },
                });
            })
        </script>
    @endsection
</x-base-layout>
