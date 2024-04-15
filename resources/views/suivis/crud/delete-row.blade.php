<form class="form" id="delete-modal-form" method="POST" action="{{ url('/suivi/delete-row') }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="item_id" value="{{ $item->id ?? 0  }}">
            <div class="me-4">
                <p> Vous voulez vraiment supprimer ce ligne ?</p>
                @if ($item->id)
                    @if ($auth->id != $item->user_id )
                        <p><u>Traité par</u> : <a href="#" class="fs-3"> {{ $item->user->sortname }} </a>, Debuté le {{ $item->created_at }}</p>
                    @endif
                    <p><u>Non du dossier </u> : <a href="#" class="fs-3">{{ $item->suivi->folder_name }} </a></p>
                    <p><u>Réference </u> : <a href="#" class="fs-3">{{ $item->suivi->ref }} </a></p>
                    <p><u>Sur </u> : </p>
                        <ul>
                            <li> La version : <a href="#" class="fs-3">{{ $item->version->title }} </a></li>
                            <li> En montage : <a href="#" class="fs-3">{{ $item->montage }} </a></li>
                        </ul>
                @endif
            </div>  
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-success btn-sm mr-2 ">
            @lang('lang.no')</button>
            &nbsp;
        <button type="submit" id="submit" class=" btn btn-sm btn-light-danger  mr-2">
            @include('partials.general._button-indicator', [
                'label' => trans('lang.yes') . ", je confirme",
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#delete-modal-form").appForm({
            showAlertSuccess: true,
            onSuccess: function(response) {
                dataTableInstance.suiviTable.row($("#{{ $row_id ?? 0 }}")).remove().draw();
            },
        })
    })
</script>
