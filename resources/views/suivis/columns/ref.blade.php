
<div class="select_box w-150px">
    <input  title="{{ $item->suivi->ref ?? "" }}" type="text" name="ref" id="input-ref" class=" form-control form-control-sm form-control-transparent edit-item-all" data-id="{{ $clone ? 0 : $item->id }}"  data-can-edit="{{  isset($item->suivi->id) ? "false" : "true"  }}"  autocomplete="off" value="{{ $item->suivi->ref ?? "" }}" placeholder="Réference"/>
</div>