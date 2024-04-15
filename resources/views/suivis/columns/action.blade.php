@if ((!$item->can_update_row() &&  !$clone)) 
    <i class="fas fa-lock fs-4"></i>
@else
    <div class="d-flex ">
        <i class="fas fa-edit edit-item fs-4" id="edit-item-{{ $item_id }}" style=" cursor: pointer;" data-id="{{ $item_id ?? 0 }}"></i>&nbsp; &nbsp;
        {{-- <i class="fas fa-times cancel-edit-item fs-3" id="cancel-item-{{ $item_id }}" style=" cursor: pointer;display:none" data-id="{{ $item_id ?? 0 }}"></i>&nbsp; &nbsp; --}}
        <i class="fas fa-save save-item fs-4" id="save-item-{{ $item_id }}" style=" cursor: pointer; display:none" data-id="{{ $item_id ?? 0 }}" data-clone-of="{{ $clone ? $item->id : 0 }}"></i>
        <div class="spinner-border spinner-border-sm" id="loading-item-{{ $item_id }}" role="status" style=" cursor: pointer; display:none"> <span class="sr-only"></span></div>
    </div>
@endif