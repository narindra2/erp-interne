<x-base-layout>
    <div class="card shadow-sm mb-3">
        <div class="card-header">
            <h5 class="card-title"> @lang('lang.tasks')</h5>
            <div class="card-toolbar">
                <ul class="nav section-task" role="tablist">
                    @foreach ($sections as $section)
                        {!! view('tasks.crud.section-item', ['section' => $section,"for_user"=> auth()->user()])->render() !!}
                    @endforeach
                    <li class="nav-item nav-item-section-task" role="presentation">
                        @php
                            echo modal_anchor(url('/task/create/section/modal'), '<i class="fas fa-plus"></i>' . 'Créer une section', [
                                'title' => 'Créer une section',
                                'data-kt-timeline-widget-1' => 'tab',
                                'data-bs-toggle' => 'tab',
                                'data-modal-lg' => true,
                                'aria-selected' => 'true',
                                'class' => 'btn btn-sm btn-light-primary ',
                            ]);
                        @endphp
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <section id="section-kanban">
        <div class="card bg-light shadow-sm">
            <div class="card-body card-scroll h-300px">
                <p class="text-center">Selectionner une section</p>
            </div>
        </div>
    </section>
    @section('dynamic_link')
        <link rel="stylesheet" href="{{ asset("library/context-menu/jquery-contextMenu.min.css") }}">
        <link rel="stylesheet" href="{{ asset("library/jquery-confirm/jquery-confirm.min.css") }}">
    @endsection
    @section('dynamic_script')
        <script src="{{ asset("library/context-menu/jquery-contextMenu.min.js") }}"></script>
        <script src="{{ asset("library/context-menu/jquery-ui-position.min.js") }}"></script>
        <script src="{{ asset("library/jquery-confirm/jquery-confirm.min.js") }}"></script>
    @endsection
    @section('scripts')
        <script>
            $(document).ready(function() {
                var getkanban = null
                $(document).on("click", '.task-section', function() {
                    let id = $(this).attr("data-task-section-id");
                    let loading = `<div class="d-flex justify-content-center">
                                    <div class="spinner-border text-primary " style="width: 2rem; height: 2rem;" role="status">
                                    </div>
                                </div>`
                    if (!$("#alert-section-" + id).hasClass("d-none")) {
                        $("#alert-section-" + id).addClass("d-none")
                    }
                    if (getkanban) {
                        getkanban.abort();
                    }
                    $("#section-kanban").html(loading);
                    getkanban = $.ajax({
                        url: url("/task/section/load/kanban"),
                        data: {
                            "section_id": id,
                            "_token": _token
                        },
                        type: 'post',
                        dataType: 'html',
                        success: function(response) {
                            $("#section-kanban").html(response);
                        },
                        error: function() {
                            console.log("error");
                        }
                    });
                })
                $(function() {
                    $.contextMenu({
                        selector: '.task-section', 
                        items: {
                            "edit": {name: "Editer", icon: "edit",callback: function(itemKey, opt, e) {
                                let section_id =  $(this).attr("data-task-section-id");
                                $("#modal-form-edit-"+section_id).trigger("click");
                            }},
                            "delete": {name: "Supprimer", icon: "delete" ,callback: function(itemKey, opt, e) {
                                let _this = $(this);
                                let section_id =  _this.attr("data-task-section-id");
                                $.confirm({
                                title: 'Confirmation',
                                content: 'Voulez-vous vraimment supprimer cette section ?',
                                buttons: {
                                    "oui ! je confirme": function () {
                                        $.ajax({
                                    url: url("/task/section/delete"),
                                    data: {
                                        "section_id": section_id,
                                        "_token": _token
                                    },
                                    type: 'post',
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.success) {
                                            _this.remove()
                                            toastr.success(response.message);
                                        }else{
                                            toastr.error(response.message);
                                        }
                                    },
                                    error: function() {
                                        toastr.error("error");
                                    }
                                });
                                    },
                                    nom: function () {
                                      return true;
                                    },
                                }
                            });
                                
                               
                            }},
                        }
                    });
                });
            })
        </script>
    @endsection
</x-base-layout>
