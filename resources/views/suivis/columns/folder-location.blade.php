<input  title="{{ $item->suivi->folder_location ?? "" }}" type="text" name="folder_location"  id="input-folder_location" class="w-400px form-control form-control-sm form-control-transparent edit-item-all row-detail-{{ $clone ? 0 : $item->id }}"  disabled = true data-can-edit="{{  isset($item->suivi->id) ? "false" : "true"  }}"  autocomplete="off" value="{{ $item->suivi->folder_location  ?? "" }}" placeholder="Emplacement du dossier ..."/>