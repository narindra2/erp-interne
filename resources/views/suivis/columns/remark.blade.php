<input type="text" name="remark" class="form-control form-control-transparent edit-item-all"
    data-id="{{ $item->id ?? 0 }}" disabled="true" data-can-edit="true"
    autocomplete="off" value="{{ !$clone ? $item->remark : '' }}" placeholder="..." />
{{-- <textarea name="remark" id="remark" class="form-control form-control-transparent " data-id="{{ $item->id ?? 0 }}"
    disabled=true data-can-edit="{{ isset($item->suivi->id) ? 'false' : 'true' }}"  rows="1"
    placeholder="Remarque ...">{{ $item->suivi->remark ?? '' }}</textarea> --}}
