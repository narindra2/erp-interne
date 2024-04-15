<form class="form" id="type-modal-form" method="POST" action="{{ url('/suivi/save/type') }}">
    <div class="card-body ">
        @csrf
        <div class="form-group">
            <div class="mb-5">
                <label for="type" class="form-label">Type : </label>
                <input type="text" name="type_suivi" id="type" autocomplete="off"  class="form-control form-control-solid" placeholder="Type du projet" />
            </div>
        </div>
        <div class="separator mb-2"></div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 ">
            @lang('lang.cancel')</button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
            @include('partials.general._button-indicator', [
                'label' => trans('lang.save'),
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>
    <div class="card-footer ">
        <table id="typeTable" class="table table-row-dashed table-row-gray-200 align-middle table-hover dataTable "></table>
    </div>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        dataTableInstance.typeTable = $("#typeTable").DataTable({
                    processing: true,
                    dom : "frtp",
                    ordering :false,
                    columns:[
                        {data :"name" , title: 'Type du projet', "class":"text-left"},
                        {data :"creator" , title: 'Creer par', "class":"text-left"},
                        {data :"action" , title: ''},
                    ],  
                    ajax: {
                        url: url("/suivi/data/type"),
                    },
                    language: {
                        url: url("/library/dataTable/datatable-fr.json"),
                    },
                    
                })
        $("#type-modal-form").appForm({
            isModal: false,
            onSuccess: function(response) {
                $("#type").val("");
                if (response.data) {
                    // Add the new option  in select custom filter
                    dataTableaddRowIntheTop(dataTableInstance.typeTable , response.data)
                }
            },
        })
    })
</script>
