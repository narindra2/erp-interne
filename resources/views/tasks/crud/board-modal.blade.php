<form class="form" id="board-modal-form" method="POST" action="{{ url('/task/save/board/status') }}">
    <div class="card-body">
        <div class="card card-flush shadow-sm ">
            <div class="card-body">
                @csrf
                <input type="hidden" id="status_id" name="status_id" value="{{ $status->id }}">
                <input type="hidden" id="section_id" name="section_id" value="{{ $section_id }}">
                <input type="hidden" id="acronym" name="acronym" value="{{ $status->acronym }}">
                <div class="form-group">
                    <div class="mb-3">
                        <label for="board-title" class="form-label">@lang('lang.board_task') : </label>
                        <div class="input-group mb-5">
                            <span class="input-group-text" id="board-title">
                                <span class="svg-icon svg-icon-2x">
                                    <i class="fas fa-tag"></i>
                                </span>
                            </span>
                            <input type="text" class="form-control form-control-solid" autocomplete="off" name="title" placeholder="@lang('lang.board_task')" value="{{ $status->title }}" />
                        </div>
                    </div>
                </div>
                <div class="form-group mt-5">
                    <label for="board-title" class="form-label">@lang('lang.colors') : </label>
                    <div class="form-check form-check-custom form-check-solid mb-4">
                        <input class="form-check-input" type="radio" value="success"  @if ($status->class == "success" ) checked @endif name="class" id="board-success"/>
                        <label class="form-check-label text-success" for="board-success">
                            Vert 
                        </label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid mb-4">
                        <input class="form-check-input" type="radio" value="danger"  @if ($status->class == "danger" ) checked @endif  name="class" id="board-danger"/>
                        <label class="form-check-label text-danger" for="board-danger">
                           Rouge 
                        </label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid mb-4">
                        <input class="form-check-input" type="radio" value="info"  @if ($status->class == "info" ) checked @endif name="class" id="board-info"/>
                        <label class="form-check-label text-info" for="board-info">
                           Viollet
                        </label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid mb-4">
                        <input class="form-check-input" type="radio" value="warning" @if ($status->class == "warning" ) checked @endif name="class" id="board-warning"/>
                        <label class="form-check-label text-warning" for="board-warning">
                            Jaune 
                        </label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid mb-4">
                        <input class="form-check-input" type="radio" value="dark" @if ($status->class == "dark") checked @endif name="class" id="board-dark"/>
                        <label class="form-check-label text-dark" for="board-dark">
                            Noir
                        </label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid mb-4">
                        <input class="form-check-input" type="radio" value="muted" @if ($status->class == "muted" || !$status->id ) checked @endif name="class" id="board-muted"/>
                        <label class="form-check-label text-muted" for="board-muted">
                            Gris foncé
                        </label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid mb-4">
                        <input class="form-check-input" type="radio" value="primary" @if ($status->class == "primary" ) checked @endif name="class" id="board-primary"/>
                        <label class="form-check-label text-primary" for="board-primary">
                            Bleu
                        </label>
                    </div>
                </div>
                @if ($status->id && $status->section->creator_id == auth()->id())
                    @if (!in_array($status->acronym,["TO_DO","FINISHED","ARCHIVED"]))
                        <div class="separator mb-2  mt-4 text-dark"></div>
                        <div class="form-group">
                            <div class="mb-3">
                                <label for="board-title" class="form-label">Supprimer: </label>
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" name="deleted" id="deleted-board"/>
                                    <label class="form-check-label text-danger"for="deleted-board">
                                        Supprimé ce colone  ?
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-dark btn-sm mr-2 ">
            @lang('lang.cancel')
        </button>
        @if (!$status->id || auth()->user()->isAdmin() || $status->section->creator_id == auth()->id() ||  $status->section->members_can("can_update_column") )
            <button type="submit" id="submit"class=" btn btn-sm btn-light-primary mr-2">
                @include('partials.general._button-indicator', [
                    'label' =>  trans('lang.save') ,
                    'message' => trans('lang.sending'),
                ])
            </button>
        @endif
       
    </div>
</form>

<script>
    $(document).ready(function() {
        $("#board-modal-form").appForm({
            onSuccess: function(response) {
                if (response.success) {
                    if (response.deleted) {
                        kanbanInstance.removeBoard(response.board.id)
                    }else{
                        if (response.board_id) {
                           return $("#reload-task").trigger("click")
                        }
                        kanbanInstance.addBoards([response.board])
                    }
                }
            },
        })
    });
</script>
