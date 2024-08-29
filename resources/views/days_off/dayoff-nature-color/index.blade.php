        <div class=" mb-5" >
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> Natures d'absence </span>
                        <div class="text-muted fs-7 fw-bold"></div>
                    </div>
                    <div class="card-toolbar"  >
                        @php
                            echo modal_anchor(url("/days-off/daysOffNature/modal_form"), '<i class="fas fa-plus"></i>' . trans('lang.add')  , ['title' => trans('lang.add')  , 'class' => 'btn btn-sm btn-light-primary']);
                        @endphp
                    </div>
                </div>
            </div>
        </div>
        <div class="" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-body py-5">
                    <table id="natureDaysOff" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                dataTableInstance.natureDaysOff = $("#natureDaysOff").DataTable({
                    dom: "tp",
                    processing: true,
             
                    columns: [ 
                        {data :"nature" , title: 'Nature',"class":"text-left"},
                        {data :"type" , title: 'Dans le',"class":"text-left"},
                        {data :"color" , title: 'Couleur',"class":"text-left"},
                        {data :"actions" , title: '<i class="fas fa-bars" style="font-size:20px"></i>',"class":"text-left"},
                    ],
                    ajax: {
                        url: url("/days-off/nature-data-list"),
                    },
                });
            })
        </script>
