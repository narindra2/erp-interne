<form class="form" id="task-modal-form" method="POST" action="{{ url('/task/save') }}" enctype="multipart/form-data">
    <div class="card-body">
       
        <div class="card card-flush shadow-sm ">
            <div class="card-body">
                @csrf
                <input type="hidden" id="task_id" name="task_id" value="{{ $task->id }}">
                <input type="hidden" id="section_id" name="section_id" value="{{ $task->section_id ?? $section_id }}">
                <div class="form-group">
                    <div class="mb-3">
                        <label for="title_task" class="form-label">@lang('lang.title_task') : </label>
                        <div class="input-group mb-5">
                            <span class="input-group-text" id="basic-addon1">
                                <span class="svg-icon svg-icon-2x">
                                    <i class="fas fa-list"></i>
                                </span>
                            </span>
                            <input type="text" class="form-control form-control-solid" autocomplete="off" id="title_task" name="title"placeholder="@lang('lang.title_task')"  value="{{ $task->title }}" />
                        </div>
                    </div>
                </div>
                    <div class="row">
                    <div class="form-group col-md-8">
                            <div class="mb-3">
                                <label for="label" class="form-label">@lang('lang.label_realisation') : </label>
                                <select id="label" name="label"
                                    class="form-select form-select-solid form-control-lg" data-hide-search="true" data-dropdown-parent="#ajax-modal" data-control="select2" data-placeholder="@lang('lang.label')">
                                    <option value="normale" @if (!$task->id || $task->label == "normale") selected @endif>Normale </option>
                                    <option value="urgent"  @if ($task->label == "urgent") selected @endif>Urgent <i class="fas fa-exclamation text-info"></i></option>
                                    <option value="tres_urgent"  @if ($task->label == "tres_urgent") selected @endif>Trés urgent <i class="fas fa-exclamation-triangle text-danger"></i></option>
                                </select>
                            </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="mb-3">
                            <label for="title_task" class="form-label">@lang('lang.color_ribbon') : </label>
                            <div class="input-group mb-5">
                                <input type="color" class="form-control form-control-solid form-control-lg" id="ribbon" name="ribbon" value="{{ $task->ribbon ?? "#ffffff" }}">
                            </div>
                        </div>
                    </div>    
                    </div>
                    
                
                <div class="form-group">
                    <div class="mb-3">
                        
                    </div>
                </div>
                <div class="form-group">
                    <label for="users" class="form-label ">Assigné(s) à : </label>
                    <div class="input-group mb-5">
                        <span class="input-group-text">
                            <span class="svg-icon svg-icon-2x">
                                <i class="fas fa-users"></i>
                            </span>
                        </span>
                        @include('tasks.kanban.users-tag', [
                            'users' => $users,
                            'default' => $default,
                            'placeholder' => 'List des membres',
                        ])
                    </div>
                </div>
                <div class="form-group">
                    <div class="mb-3">
                        <label for="proprietor_id" class="form-label">@lang('lang.description') : </label>
                        <textarea class="form-control form-control-solid " placeholder="@lang('lang.description')" name="description" id="desc_task">{{ $task->description }}</textarea>
                    </div>
                </div>
                @if ($task->id)
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="label" class="form-label">@lang('lang.drag_to') : </label>
                            <select id="label" name="status_id" class="form-select form-select-solid form-control-lg" data-hide-search="true" data-dropdown-parent="#ajax-modal" data-control="select2" data-placeholder="@lang('lang.label')">
                                @foreach ($task->section->colones as $colone)
                                    <option  class="text-{{ $colone->class }}" value="{{ $colone->id }}" @if ($task->status_id == $colone->id) selected @endif>{{ $colone->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                @if (!$task->id)
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="files" class="form-label">@lang('lang.add_files') : </label>
                            <div class="row mb-2 ">
                                <div class="col-lg-4">
                                    <a href="javascript:;" id="add" onClick="add()"
                                        >
                                        <i class="la la-plus"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row  mb-1 file-input" id="div">
                                <div class="col-md-11 mb-2">
                                    <input class="form-control form-control-sm" name="files[]" type="file">
                                </div>
                                <button type="button" onClick="del(this)"
                                    class="btn btn-sm btn-icon btn-light-danger col-1 "><i
                                        class="la la-trash-o"></i></button>
                            </div>
                            <script>
                                var maxInput = 1;
                                var maxfiles = 10;

                                function del(_this) {
                                    if (maxInput > 1) {
                                        maxInput--;
                                        $(_this).closest('.file-input').remove();
                                    }
                                }

                                function add() {
                                    if (maxInput < maxfiles) {
                                        maxInput++;
                                        $("#div").clone().insertBefore("#div").find("input[type='file']").val("");
                                    }
                                }
                            </script>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="check_lists" class="form-label">@lang('lang.add_check_lists') : </label>
                            <div class="row mb-2 ">
                                <div class="col-lg-4">
                                    <a href="javascript:;" onClick="addCheclist()" >
                                        <i class="la la-plus"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row  mb-1 inputCheclist" id="divs">
                                <div class="col-md-11 mb-2">
                                    <input class="form-control form-control-solid form-control-sm" autocomplete="off" name="checklists[]" type="text" placeholder="> A faire ...">
                                </div>
                                <button type="button" onClick="delCheclist(this)"class="btn btn-sm btn-icon btn-light-danger col-1 ">
                                    <i class="la la-trash-o"></i></button>
                            </div>
                            <script>
                                var minInputChechList = 1;
                                var maxChechList = 10;

                                function delCheclist(_this) {
                                    if (minInputChechList > 1) {
                                        minInputChechList--;
                                        $(_this).closest('.inputCheclist').remove();
                                    }
                                }
                                function addCheclist() {
                                    if (minInputChechList  < maxChechList) {
                                        minInputChechList++;
                                        $("#divs").clone().insertBefore("#divs").find("input[type='text']").val("");
                                    }
                                }
                            </script>
                        </div>
                    </div>
                @endif

            </div>
        </div>
        <div class="separator mb-2 text-dark"></div>
        <div class="card card-flush">
            <div class="card-body">
                <ul class="nav nav-tabs nav-line-tabs  ">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#propriety">
                            <h4 class="card-title">@lang('lang.propriety')</h4>
                        </a>
                    </li>
                    @if ($task->id)
                        <li class="nav-item">
                            <a class="nav-link " data-bs-toggle="tab" href="#checklists">
                                <h4 class="card-title">@lang('lang.checklistes')
                                    @php
                                        $count_checklist = $task->checkLists->count();
                                    @endphp
                                    {{ $count_checklist > 0 ? "($count_checklist)" : '' }}
                                </h4>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " data-bs-toggle="tab" href="#comments">
                                <h4 class="card-title">@lang('lang.comments')
                                    @php
                                        $count = $task->comments->count();
                                    @endphp
                                    {{ $count > 0 ? "($count)" : '' }}
                                </h4>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " data-bs-toggle="tab" href="#files">
                                <h4 class="card-title">@lang('lang.files')
                                    @php
                                        $count_files = $task->files->count();
                                    @endphp
                                    {{ $count_files > 0 ? "($count_files)" : '' }}
                                </h4>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link " data-bs-toggle="tab" href="#task-action">
                            <h4 class="card-title">@lang('lang.operation')</h4>
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="TaskTabContent">
                    <div class="tab-pane fade show active  mt-4" id="propriety" role="tabpanel">
                        @if ($task->id)
                            <div class="form-group">
                                <label for="proprietor_id" class="form-label ">Crée le :</label>
                                <input type="text" autocomplete="off"
                                    class="form-control form-control-transparent" readonly="true"
                                    value="{{ convert_to_real_time_humains($task->created_at) }}, par {{ $task->autor->sortname }}" />
                            </div>
                        @endif
                        <div class="separator separator-content mb-5 mt-2 ">Deadline </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <input type="text" class="form-control form-control-solid" autocomplete="off"
                                        placeholder="Début ..."
                                        value="{{ $task->start_deadline_date ? date('d/m/Y', strtotime($task->start_deadline_date)) : '' }}"
                                        name="start_deadline_date" id="start_deadline_date" />
                                </div>
                                <div class="mb-3  col-md-6">
                                    <input type="text" class="form-control form-control-solid" autocomplete="off"
                                        placeholder="Fin ..."
                                        value="{{ $task->end_deadline_date ? date('d/m/Y', strtotime($task->end_deadline_date)) : '' }}"
                                        name="end_deadline_date" id="end_deadline_date" />
                                </div>
                            </div>
                        </div>
                        <div class="separator separator-content mb-6 mt-5">Recurence </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label for="recurring_type" class="form-label ">Tâche : </label>
                                    <select id="recurring-type" name="recurring_type"
                                        class="form-select form-select-solid form-control-lg" data-hide-search="true"
                                        data-dropdown-parent="#ajax-modal" data-control="select2"
                                        data-placeholder="@lang('lang.recurring_type')">
                                        <option value="0"> -- Non @lang('lang.recurring_type') --</option>
                                        @foreach ($recurring_type as $type)
                                            <option value="{{ get_array_value($type, 'type') }}"
                                                @if ($task->recurring_type == get_array_value($type, 'type')) selected @endif>
                                                {{ get_array_value($type, 'title') }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" id="nb-day"
                                    style="opacity:{{ $task->recurring_type == 'every_nb_day' ? '100' : '0' }}">
                                    <label for="recurring_type" class="form-label ">nombres du jours: </label>
                                    <input type="number" min="2" autocomplete="off"
                                        class="form-control form-control-solid" name="nb_days"
                                        value="{{ $task->nbRecurringEveryDays > 1 ? $task->nbRecurringEveryDays : 2 }}" />
                                </div>
                                <div class="col-md-4" id="every-day-on"
                                    style="opacity:{{ $task->recurring_type == 'every_day_on' ? '100' : '0' }}">
                                    <label for="day_of_week" class="form-label ">Jour : </label>
                                    <select id="day_of_week" name="day_of_week"
                                        class="form-select form-select-solid form-control-lg" data-hide-search="true"
                                        data-dropdown-parent="#ajax-modal" data-control="select2"
                                        data-placeholder="@lang('lang.recurring_type')">
                                        @php
                                            $day_of_week = get_array_value($task->detailRecurring, 'day_of_week');
                                        @endphp
                                        <option value="1" @if ($day_of_week == 1 || !$day_of_week) selected @endif>Lundi
                                        </option>
                                        <option value="2" @if ($day_of_week == 2) selected @endif>Mardi
                                        </option>
                                        <option value="3" @if ($day_of_week == 3) selected @endif>
                                            Mercredi</option>
                                        <option value="4" @if ($day_of_week == 4) selected @endif>Jeudi
                                        </option>
                                        <option value="5" @if ($day_of_week == 5) selected @endif>
                                            Vendredi</option>
                                        <option value="6" @if ($day_of_week == 6) selected @endif>
                                            Samedi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="mb-3">
                                <label for="start_date_recurring" class="form-label">Débuter à partir </label>
                                <input type="text" class="form-control form-control-solid" autocomplete="off"
                                    placeholder="Debut ..."
                                    value="{{ $task->get_start_recycle_date() ? $task->get_start_recycle_date()->format('d/m/Y') : '' }}"
                                    name="start_date_recurring" id="start_date_recurring" />
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade mt-4" id="checklists" role="tabpanel">
                        <div class="row md-3">
                            <div class="form-group col-md-10">
                                <div class="mb-3">
                                    <input type="text" class="form-control form-control-solid" name="new_checklist" id="new_checklist" autocomplete="off" placeholder="Ajouter un Check list ..."/>
                                </div>
                            </div>
                            <div class="form-group col-md-2" id="save_checklist" style="display: none">
                                <button type="button" id="send_other_checklist"
                                    class=" btn btn-sm btn-light-primary  mr-2">
                                    @include('partials.general._button-indicator', [
                                        'label' => trans('lang.add'),
                                        'message' => '',
                                    ])
                                </button>
                            </div>
                            {{-- <div class="form-group col-md-2" >
                                <button type="button" id="send_other_checklist" class="btn btn-sm btn-light-primary ">Ajouter</button>
                            </div> --}}
                        </div>
                        <label for="permissions" class="form-label mb-3 ">Les check-listes : </label><br>
                        <div class="timeline ms-n1" id="task-checklists-list">
                            @foreach ($task->checkLists as $checklist)
                                {{ view('tasks.checklists.item', ['checklist' => $checklist]) }}
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade mt-4" id="comments" role="tabpanel">
                        <div class="row md-3">
                            <div class="form-group col-md-10">
                                <div class="mb-3">
                                    <input type="hidden" value="0" id="upadate_comment_id">
                                    <textarea class="form-control form-control-solid" placeholder="@lang('lang.add-comment') ..." name="comment"
                                        id="comment"></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-2" id="save_comment" style="display: none">
                                <button type="button" id="save_comment_btn"
                                    class=" btn btn-sm btn-light-primary  mr-2">
                                    @include('partials.general._button-indicator', [
                                        'label' => trans('lang.add'),
                                        'message' => '',
                                    ])
                                </button>
                            </div>
                        </div>
                        <div class="timeline ms-n1" id="task-comment-list">
                            @foreach ($task->comments as $comment)
                                {{ view('tasks.comments.item', ['comment' => $comment]) }}
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade mt-4" id="files" role="tabpanel">
                        <div class="form-group">
                        <label for="files" class="form-label">@lang('lang.add_other_files') : </label>
                        <div class="row  mb-1 file-input">
                            <div class="col-md-10 mb-2">
                                <input class="form-control form-control-sm" id="other_file" name="other_file"  type="file">
                            </div>
                            <div class="col-md-2 ">
                                <button type="button" id="send_other_file"
                                    class=" btn btn-sm btn-light-primary  mr-2">
                                    @include('partials.general._button-indicator', [
                                        'label' => trans('lang.add'),
                                        'message' => '',
                                    ])
                                </button>
                            </div>
                        </div>
                        </div>
                        <div class="timeline ms-n1" id="files">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fw-semibold text-gray-600 fs-6 gy-5"
                                    id="taskFilesTable">
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade mt-4" id="task-action" role="tabpanel">
                        @if ($task->id)
                            @if ($task->section->members_can("can_archive_task"))
                                <div class="form-group mt-5">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1" @if ($task->id && $task->archived) checked @endif name="archived" id="archived-task"/>
                                        <label class="form-check-label" for="archived-task">
                                        Archivé ce tâche
                                        </label>
                                    </div>
                                </div>
                            @endif
                            @if ($task->section->members_can("can_delete_task"))
                                <div class="form-group mt-5">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1"  name="deleted" id="deleted-task"/>
                                        <label class="form-check-label text-danger" for="deleted-task">
                                            Supprimé ce tâche
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-dark btn-sm mr-2 ">
            @if ($task->id)
                @lang('lang.quit')
            @else
                @lang('lang.cancel')
            @endif
        </button>
        &nbsp;
        {{-- @if (!$task->id || $task->is_the_autor()) --}}
        @if (!$task->id || $task->section->members_can("can_update_or_edit_member_task") )
            <button type="submit"
                id="submitTaskForm"class=" btn btn-sm btn-light-{{ $task->id ? 'success' : 'primary' }}  mr-2">
                @include('partials.general._button-indicator', [
                    'label' => $task->id ? trans('lang.update-a-task') : trans('lang.create-a-task'),
                    'message' => trans('lang.sending'),
                ])
            </button>
        @endif
        {{-- @endif --}}
    </div>
</form>
<style>
    .action-hover-hide {
        opacity: 0;
    }

    .action-hover-hide:hover {
        opacity: 100;
    }
</style>
{{-- @section('dynamic_script')
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
@endsection --}}
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#start_date_recurring").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoApply: true,
            autoUpdateInput: false,
            drops: "up",
            locale: {
                format: 'DD/MM/yyyy',
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/yyyy'))
            $(this).change()
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val("")
            $(this).change()
        });

        $("#start_deadline_date ,#end_deadline_date").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoApply: true,
            autoUpdateInput: false,
            drops: "up",
            locale: {
                format: 'DD/MM/yyyy',
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/yyyy'))
            $(this).change()
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val("")
            $(this).change()
        });
        $("#save_comment_btn").on("click", function() {
            let comment = $("#comment").val()
            let comment_id = $("#upadate_comment_id").val()
            if (comment) {
                let indicator_send = document.querySelector("#save_comment_btn");
                indicator_send.setAttribute("data-kt-indicator", "on");
                $.ajax({
                    url: url("/task/save/comment"),
                    data: {
                        "_token": _token,
                        "comment": comment,
                        "comment_id": $("#upadate_comment_id").val(),
                        "task_id": $("#task_id").val()
                    },
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            if (comment_id != "0") {
                                $("#task-comment-item-content-" + comment_id).text(comment)
                            } else {
                                if ($(".task-comment-item").length) {
                                    $(".task-comment-item:first").before(response.comment)
                                } else {
                                    $("#task-comment-list").html(response.comment)
                                }
                            }
                            $("#comment").val("")
                            $("#upadate_comment_id").val(0)
                        }
                        indicator_send.removeAttribute("data-kt-indicator");
                    },
                    error: function() {
                        indicator_send.removeAttribute("data-kt-indicator");
                        console.log("error");
                    }
                });
            }
        })
        $(document).on("click", ".delete-task-comment", function() {
            let id = $(this).attr("data-comment-id");
            $.ajax({
                url: url("/task/comment/delete"),
                type: 'POST',
                data: {
                    "_token": _token,
                    "comment_id": $(this).attr("data-comment-id"),
                },
                success: function(response) {
                    if (response.success) {
                        $("#task-comment-item-" + id).remove()
                    }
                }
            });
            $("#upadate_comment_id").val(0)
        })
        $(document).on("click", ".edit-task-comment", function() {
            let id = $(this).attr("data-comment-id");
            $("#comment").val($("#task-comment-item-content-" + id).text())
            $("#upadate_comment_id").val(id)
        })
        @if ($task->recurring_type != 'every_nb_day')
            $("#nb-day").css("opacity", "0")
        @endif
        @if ($task->recurring_type != 'every_day_on')
            $("#every-day-on").css("opacity", "0")
        @endif
        $("#recurring-type").on("change", function() {
            let val = $(this).val();
            if (val == "every_nb_day") {
                $("#nb-day").css("opacity", "100")
            } else {
                $("#nb-day").css("opacity", "0")
            }
            if (val == "every_day_on") {
                $("#every-day-on").css("opacity", "100")
            } else {
                $("#every-day-on").css("opacity", "0")
            }
        })
        dataTableInstance.taskFilesTable = $('#taskFilesTable').DataTable({
            dom: "rt",
            processing: true,
            ordering: false,
            columns: [
                {data :"name" , title: ''},
                {data :"actions" , title: '',"class":"pe-0 text-end min-w-200px"},
            ],
            ajax: {
                url: url("/task/files-list"),
                data: function(data) {
                    data.task_id = "{{ $task->id }}";
                    data.section_id = "{{ $task->section_id }}" ;
                }
            },
        });
        var sendOtherFiles = null
        $("#send_other_file").on("click",function(){
            let indicator_send_file = document.querySelector("#send_other_file");
            indicator_send_file.setAttribute("data-kt-indicator", "on");
            console.log("clic àthe files");
                let formData = new FormData();
                formData.append("_token",_token);
                formData.append("task_id",$("#task_id").val());
                formData.append("other_file",$('#other_file')[0].files[0]);
                if (sendOtherFiles != null) {
                    sendOtherFiles.abort();
                }
                sendOtherFiles = $.ajax({
                    type:'POST',
                    url: url("/task/file/add/other-file"),
                    data: formData,
                    contentType: false,
                    processData: false,
                    // async: false,
                    // cache: false,
                    success: function(response){
                        indicator_send_file.removeAttribute("data-kt-indicator");
                        if (response.success) {
                            $('#other_file').val("");
                            dataTableaddRowIntheTop(dataTableInstance.taskFilesTable, response.data)
                            return toastr.success(response.message);
                        }else{
                            return toastr.error(response.message);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        indicator_send_file.removeAttribute("data-kt-indicator");
                        console.log(thrownError);
                        return toastr.error("Sereveur error");
                    }
                });
                        
        });
        $(".resolve-checklist-input").on("click",function(){
            let _this = $(this);
            let id = _this.attr("data-id");
            $.ajax({
                type:'POST',
                async: false,
                cache: false,
                url: url("/task/checklist/mark/done"),
                data: { "checklist_id" : id ,"_token" : _token},
                dataType:"json",
                success: function(response){
                    if (response.success) {
                        return $("#checklist-item-"+id).replaceWith(response.data);
                    }else{
                        return toastr.error(response.message);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    return toastr.error("Sereveur error");
                }
            });
            return;        
        });
        $("#send_other_checklist").on("click",function(){
            let _this = $(this);
            let text = _this.text();
            let indicator_send_checklist = document.querySelector("#send_other_checklist");
            indicator_send_checklist.setAttribute("data-kt-indicator", "on");
            $.ajax({
                type:'POST',
                url: url("/task/checklist/add/new"),
                data: { "new_checklist" : $("#new_checklist").val() ,"task_id" : $("#task_id").val() ,"_token" : _token},
                dataType:"json",
                success: function(response){
                    if (response.success) {
                        $("#new_checklist").val("");
                        $("#task-checklists-list").prepend(response.data);
                        toastr.success(response.message);
                    }else{
                        toastr.error(response.message);
                    }
                    indicator_send_checklist.removeAttribute("data-kt-indicator");
                },
                error: function(xhr, ajaxOptions, thrownError) {
                  
                    indicator_send_checklist.removeAttribute("data-kt-indicator");
                    return toastr.error("Sereveur error");
                }
            });
            return;        
        });
        $(document).on("click",".delete-checklist-btn",function(){
            let _this = $(this);
            let id = _this.attr("data-id");
            let item =  $("#checklist-item-"+id);
            item.fadeOut("slow");
            $.ajax({
                type:'POST',
                url: url("/task/checklist/delete"),
                data: { "checklist_id" : id ,"_token" : _token},
                dataType:"json",
                success: function(response){
                    if (response.success) {
                        return item.remove();
                    }else{
                        toastr.error(response.message);
                        item.fadeIn("slow");
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    item.fadeIn("slow");
                    return toastr.error("Sereveur error");
                }
            });
            return;        
        });
        $("#task-modal-form").appForm({
            showAlertSuccess: true,
            submitBtn: "#submitTaskForm",
            onSuccess: function(item) {
                addOrUpdateOrDeleteTaskInKanban(item) //** views/includes/notification-js  
            },
        })
           
        /*
        ClassicEditor.create(document.querySelector('#desc_task'), {
            toolbar: [
                'heading', '|',
                'fontfamily', 'fontsize', '|',
                'alignment', '|',
                'fontColor', 'fontBackgroundColor', '|',
                'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', '|',
                'link', '|',
                'outdent', 'indent', '|',
                'bulletedList', 'numberedList', 'todoList', '|',
                'code', 'codeBlock', '|',
                'insertTable', '|',
                'uploadImage', 'blockQuote', '|',
                'undo', 'redo'
            ],
            // shouldNotGroupWhenFull: true
        })
        **/

    });
</script>
