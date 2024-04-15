<form class="form" id="custom-filter-modal-form" method="POST" action="{{ url('/save/custom-filter') }}">
    <div class="card-body ">
        @csrf
        <div class="form-group">
            <div class="mb-5">
                <label for="proprietor_id" class="form-label">@lang('lang.name_filter') </label>
                <input type="text" name="name_filter" id="name_filter" autocomplete="off"  class="form-control form-control-solid" placeholder="@lang('lang.name_filter')" />
            </div>
        </div>
       {!!    view("suivis.crud.custom-filter-inputs", ["options" => $options , "modal" => $modal] )->render() !!}
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 ">
            @lang('lang.cancel')</button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
            @include('partials.general._button-indicator', [
                'label' => trans('lang.create_this_fiter'),
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>
    <div class="card-footer ">
        <table id="customFilterTable" class="table table-row-dashed table-row-gray-200 align-middle table-hover dataTable "></table>
    </div>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        dataTableInstance.customFilterTable = $("#customFilterTable").DataTable({
                    processing: true,
                    dom : "rtp",
                    ordering :false,
                    columns:[
                        {data :"name" , title: 'Nom du filtre', "class":"text-left"},
                        {data :"filters" , title: 'Filtré sur', "class":"text-left"},
                        {data :"actions" , title: ''},
                    ],  
                    ajax: {
                        url: url("/suivi/custom-filter-data-list"),
                    },
                    language: {
                        url: url("/library/dataTable/datatable-fr.json"),
                    },
                    
                })
        $("#custom-filter-modal-form").appForm({
            isModal: false,
            onSuccess: function(response) {
                $("#name_filter").val("");
                if (response.data) {
                    // Add the new option custom filter in select custom filter
                    if ($("#custom_filter_id")) {
                        $("#custom_filter_id").prepend("<option value='"+response.data.id+"' >"+response.data.name+"</option>");
                    }
                    dataTableaddRowIntheTop(dataTableInstance.customFilterTable , response.data)
                }
            },
        })
    })
</script>
