<x-base-layout>
    <div class="card shadow-sm  mb-3 ">
        <div class="card-header border-1 pt-1">
            <div class="me-2 card-title align-items-start ">
                <span class="card-label  fs-3 mb-1"> Projets ou Groupes </span>
                <div class="text-muted fs-7 fw-bold"></div>
            </div>
            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                data-bs-original-title="Créer un group de projet ">
                @php
                    echo modal_anchor(url('/project/add/modal-form'), '<i class="fas fa-plus"></i>' . "Créer un groupe ou un projet", ['title' => "Créer un group", 'class' => 'btn btn-sm btn-light-primary']);
                @endphp
            </div>
        </div>
    </div>
    <div class="card shadow-sm  ">
        <div class="card-body py-5">
            <div class="d-flex justify-content-end mb-5">
                <div class="filter-datatable">
                    @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "projectMembersTable"])
                </div>
                &nbsp; &nbsp;
                <div class="me-4 my-2 ml-3 ">
                    <div class="d-flex align-items-center position-relative my-1">
                        <input type="text" id="do-search-project-members" autocomplete="off"
                            class="form-control form-control-solid form-select-sm w-300px ps-9 "
                            placeholder="{{ trans('lang.search'). " projet , collaborateurs , ..." }}">
                    </div>
                </div>
                <div class="me-4 my-2">
                    <a id="do-reload" title = "Actualiser la table" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                        <i class="fas fa-sync-alt" style="width: 10px;"></i>
                    </a>
                </div>
            </div>
            <table id="projectMembersTable" class="table table-row-dashed table-row-gray-200 align-middle  table-hover "></table>
        </div>
    </div>
    <style>
        .form-check.form-check-solid .form-check-input:checked {
        background-color: #50cd89;
    }
    </style>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.projectMembersTable = $("#projectMembersTable").DataTable({
                    processing: true,
                    dom : "ltpr",
                    columnDefs : [
                        { visible: false, targets: [0] ,searchable : true}
                    ],
                    ordering :false,
                    columns:[
                        {data :"users_list" , title: ''},
                        {data :"name" , title: 'Nom du groupe ou projet', "class":"text-left w-250px"},
                        {data :"members" , title: 'Membres du groupe ou projet', "class":"text-left "},
                        {data :"validator_dayoff" , title: 'Validateur des congés du groupe ou projet', "class":"text-left "},
                        {data :"action" , title: '', "class":"text-left "},
                    ],  
                    ajax: {
                        url: url("/user/projet-membre/data-list"),
                        data: function(data) {
                            <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                                data.{{ $input }} = $("#{{ $input }}").val();
                            <?php } ?>
                        }
                    },
                    language: {
                        url: url("/library/dataTable/datatable-fr.json")
                    },
                    
                })
                $('.projectMembersTable').on('change', function() {
                    dataTableInstance.projectMembersTable.ajax.reload();
                });
                $('#do-search-project-members').on('keyup', function(e) {
                    dataTableInstance.projectMembersTable.search($(this).val()).draw();
                });
                $('#do-reload').on('click', function(e) {
                    dataTableInstance.projectMembersTable.ajax.reload();
                });
               
            })
        </script>
    @endsection
</x-base-layout>
