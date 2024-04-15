<x-base-layout>
    
    <div class="card card-xxl-stretch mb-5 mb-xl-8">
        <div class="card-header border-0 pt-2">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label  fs-3 mb-1"> @lang('lang.list_of_status') </span>
            </h3>
            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                data-bs-original-title="@lang('lang.add_new_status')">
                @php
                    echo modal_anchor(url('status/form_modal/'), '<i class="fas fa-plus"></i>' . trans('lang.add_new_status'), ['title' => trans('lang.add_new_status'), 'class' => 'btn btn-sm btn-light-primary', "data-modal-lg" => true]);
                @endphp
            </div>
        </div>
    </div>
    <div class="card card-flush ">
        <div class="card-body py-5">
            <div id="items-1" class="list-group col">
                @foreach ($status as $s)
                <div id="item-{{ get_array_value($s,"id")}}" data-id="{{ get_array_value($s,"id")}}">
                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start ">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ get_array_value($s,"name")}}</h5>
                            <small class="text-muted"> <button  class="btn-sm btn btn-primary">details status</button> 
                                <button  data-id="{{ get_array_value($s,"id")}}" class="btn-sm btn btn-danger delete">delete status (atao ajax le request. d fafana le div rhf success le delete)</button> 
                            </small>
                        </div>
                        <p class="mb-1">{{ get_array_value($s,"entity")}}</p>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @section('dynamic_script')
        <script src="https://unpkg.com/sortablejs-make/Sortable.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
    @endsection
    <script>
        $(document).ready(function() {
            $('#items-1').sortable(
                {
                    group: 'list',
                    animation: 200,
                    ghostClass: 'ghost',
                    onSort: onChange,
                }
            );  
            function onChange() {
                var ordering = $('#items-1').sortable('toArray');
                $.ajax({
                    url: url("/update/status/order"),
                    data: {
                        "_token" :  _token,
                        "new_ordering" :  ordering,
                    },
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            console.log("updated");
                        }
                    },
                    error: function() {
                        console.log("error");
                    }
                }); 
            };
            $(".delete").on("click",function () {
                var id = $(this).attr("data-id")
                $.ajax({
                    url: url("/delete/status"),
                    data: {
                        "_token" :  _token,
                        "id" :  id,
                    },
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                          $("#item-"+id).remove();
                          //save new change order without last deleted
                          onChange()
                        }
                    },
                    error: function() {
                        console.log("error");
                    }
                }); 
            })
            
        })
    </script>
</x-base-layout>
