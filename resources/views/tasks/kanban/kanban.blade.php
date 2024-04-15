<div class="card shadow-sm mb-3">
    <div class="card-header">
        <div class="symbol-group symbol-hover flex-nowrap mt-1">
            <label  class=" text-gray-800 fw-bold me-5"><strong><u>Membres du {{ $section->title }}</u> :</strong> </label>
            @foreach ($section->members as $user)
                {!! view("tasks.crud.member-avatar",["user" => $user ])->render() !!}
            @endforeach
            @if ($section->members_can("can_add_members"))
                @php
                    echo modal_anchor(url('/task/section/modal/members'), ' <span class="symbol-label bg-dark text-gray-300 fs-8 fw-bold"> <i class="fas fa-plus"></i></span> ', ['title' => trans('lang.add-new-members'), 'data-modal-lg' => true,'class' => 'symbol symbol-35px symbol-circle', 'data-post-section_id' => $section->id]);
                @endphp
            @endif
        </div>
        <div class="card-toolbar">
            <a href="#" title="Recharge" id="reload-task" class="btn btn-sm btn-light">
                <i class="fas fa-redo"></i>
            </a>
            &nbsp;
            {{-- @if ($section->members_can("can_access_members_task")) --}}
                @include('filters.filters-basic', ['inputs' => $basic_filter, 'filter_for' => 'boardTask'])
            {{-- @endif --}}
            @if ($section->members_can("can_add_column"))
                @php
                    echo modal_anchor(url('/task/add/board/status-modal'), '<i class="fas fa-plus"></i>' . trans('lang.add-new-board-collumn'), ['title' => trans('lang.add-new-board-collumn'), 'class' => 'btn btn-sm btn-light mx-2', 'data-post-section_id' => $section->id]);
                @endphp
            @endif
            @if ($section->members_can("can_add_task"))
                @php
                    echo modal_anchor(url('/task/modal-form'), '<i class="fas fa-plus"></i>' . trans('lang.add-new-task'), ['title' => trans('lang.add-new-task'), 'data-modal-lg' => true, 'class' => 'btn btn-sm btn-light-primary  mx-2','data-post-section_id' => $section->id]);
                @endphp
            @endif
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body card-scroll h-500px" id="board-task" data-section-id="{{ $section->id }}">
            <div id="jkanban-task"></div>
        </div>
    </div>
    <link href="{{ url('library/jkanban/jkanban.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ url('library/jkanban/jkanban.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            KTApp.initSelect2();
            var loadDataAjax = null
            var targetBoardTast = document.querySelector("#board-task");
            var blockUIBoardTast = new KTBlockUI(targetBoardTast);

            function release() {
                if (blockUIBoardTast.isBlocked()) {
                    blockUIBoardTast.release();
                }
            }

            function block() {
                if (!blockUIBoardTast.isBlocked()) {
                    blockUIBoardTast.block();
                }
            }

            function jkanban(data) {
                kanbanInstance = new jKanban({
                    element: '#jkanban-task',
                    widthBoard: '300px',
                    // responsivePercentage: true,  
                    boards: data,
                    @if (!$section->members_can("can_update_column"))
                        dragBoards: false,
                    @endif
                    click: function(el) {
                        $("#detail-btn-" + el.getAttribute("data-task-id")).trigger("click")
                    },
                    dropEl: function(el, target, source, sibling) {
                        let data = {}
                        updateOrderItem($(target.parentElement))
                        data.source_id = $(source).closest("div.kanban-board").attr("data-id");
                        data.target_id = $(target).closest("div.kanban-board").attr("data-id");
                        if (data.source_id != data.target_id) {
                            data.task_id = el.getAttribute("data-task-id");
                            data._token = _token
                            $("#indicator-id-" + data.task_id).attr("data-kt-indicator", "on")
                            updateTask(data, data.task_id)
                        }
                    },
                    dragendBoard: function(el) {
                        updateOrderBoard()
                    }
                });
                return kanbanInstance
            }

            function updateOrderItem(board) {
                let data = [];
                let currentOrder = 1;
                let elements = kanbanInstance.getBoardElements(board.data("id"))
                elements.forEach(function(value, index, array) {
                    data.push({
                        "id": $(value).data("eid"),
                        "order_on_board": currentOrder++
                    })
                });
                $.ajax({
                    url: url("/task/update/order/item"),
                    type: 'POST',
                    data: {
                        "_token": _token,
                        "section_id": $("#board-task").attr("data-section-id"),
                        "data": data
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log("updated oder");
                        }
                    },
                    error: function() {
                        console.log("error");
                    }
                });
            }

            function updateOrderBoard() {
                let data = [];
                let boards = $('#jkanban-task').find('.kanban-board');
                let currentOrder = 1;
                boards.each(function(item) {
                    data.push({
                        "id": $(this).attr("data-id").replace("board-id-", ""),
                        "order_board": currentOrder++
                    });
                });
                $.ajax({
                    url: url("/task/update/board/order"),
                    type: 'POST',
                    data: {
                        "_token": _token,
                        "section_id": $("#board-task").attr("data-section-id"),
                        "data": data
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log("updated oder");
                        }
                    },
                    error: function() {
                        console.log("error");
                    }
                });
            }

            function updateTask(data, id) {
                setTimeout(() => {
                    $("#indicator-id-" + id).attr("data-kt-indicator", "off")
                }, 700);
                $.ajax({
                    url: url("/task/update"),
                    data: data,
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
            }

            function reInitialiseKanban(data = {}) {
                block()
                <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                data.{{ $input }} = $("#{{ $input }}").val();
                <?php } ?>
                if (loadDataAjax &&  loadDataAjax.readyState != 4) {
                    loadDataAjax.abort();
                }
                loadDataAjax = $.ajax({
                    url: data.url,
                    data: data,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            $("#jkanban-task").html("")
                            kanbanInstance = jkanban(response.data)
                        }
                        release()
                    },
                    error: function() {
                        console.log("error");
                        release()
                    }
                });
                return kanbanInstance
            }

            reInitialiseKanban({
                "url": url("/kanban/data/source"),
                "section_id": $("#board-task").attr("data-section-id"),
                "_token": _token,
            })

            $("#reload-task").on("click", function() {
                kanbanInstance = reInitialiseKanban({
                    "url": url("/kanban/data/source"),
                    "section_id": $("#board-task").attr("data-section-id"),
                    "_token": _token,
                })
            })
            $(".boardTask").on("change", function() {
                reInitialiseKanban({
                    "url": url("/kanban/data/source"),
                    "section_id": $("#board-task").attr("data-section-id"),
                    "_token": _token,
                })
            })
            $("#search_task").on("keyup", function() {
                if ($(this).val().length >= 3 || $(this).val() == "") {
                    reInitialiseKanban({
                        "url": url("/kanban/data/source"),
                        "section_id": $("#board-task").attr("data-section-id"),
                        "_token": _token
                    })
                }
            })
        })
    </script>
