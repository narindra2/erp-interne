<form class="form" id="type-client-modal-form" method="POST" action="{{ url('/suivi/save/type-client') }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="client_type_id" id="client_type_id" value="0">
        <div class="form-group">
            <div class="mb-5">
                <label for="type" class="form-label">Type client : </label>
                <input type="text" name="type_client" id="type_client" autocomplete="off"  class="form-control form-control-solid" placeholder="Type client" />
            </div>
        </div>
        <div class="form-group">
            <div class="mb-5">
                <label for="type" class="form-label">Status : </label>
                <select name="status" id="status" class="form-select form-select-solid rounded-start-0 border-start" data-control="select2" >
                    <option value="0" disabled>-- Status du client --</option>
                    <option value="on">Actif</option>
                    <option value="off">Non actif</option>
                </select>
            </div>
        </div>
        <div class="separator mb-2"></div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 ">
            @lang('lang.cancel')</button>
        <button type="button" id= "cancel-edit" style= "display:none" class="btn btn-light-warning btn-sm mr-2 ">
            Annuler l'edition    
        </button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
            @include('partials.general._button-indicator', [
                'label' => trans('lang.save'),
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>
    <div class="card-footer">
        <table id="typeClientTable" class="table table-row-dashed table-row-gray-200 align-middle table-hover dataTable "></table>
    </div>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        dataTableInstance.typeClientTable = $("#typeClientTable").DataTable({
                    processing: true,
                    dom : "frtp",
                    ordering :false,
                    columns:[
                        {data :"name" , title: 'Type dossier', "class":"text-left"},
                        {data :"status" , title: 'statuyt', "class":"text-left"},
                        {data :"action" , title: ''},
                    ],  
                    ajax: {
                        url: url("/suivi/data/type-client"),
                    },
                    language: {
                        url: url("/library/dataTable/datatable-fr.json"),
                    },
                })

        $(document).on("click",".edit-type-client",function(){
            let id = $(this).attr("data-id");
            $.ajax({
                url: url("/suivi/get-one/type-client"),
                data: {
                    "id": id,
                    "_token": _token
                },
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    $("#client_type_id").val(id);
                    $("#type_client").val(response.data.name);
                    $("#status").val(response.data.status);
                    $("#cancel-edit").css("display","")
                    $("#type_client").focus();
                },
                error: function() {
                    console.log("error");
                }
            });
        })        
        $(document).on("click","#cancel-edit",function(){
            let id = $(this).attr("data-id");
            $("#client_type_id").val(0);
            $("#type_client").val("");
            $("#status").val("on");
            $("#cancel-edit").css("display","none")
        })
        
        $("#type-client-modal-form").appForm({
            isModal: false,
            onSuccess: function(response) {
                $("#type_client").val("");
                if (response.data) {
                    if ($("#client_type_id").val() == "0" ) {
                        dataTableaddRowIntheTop(dataTableInstance.typeClientTable , response.data)
                    }else{
                        dataTableInstance.typeClientTable.ajax.reload();
                    }
                    $("#cancel-edit").css("display","none")
                }
            },
        })
    })
</script>
