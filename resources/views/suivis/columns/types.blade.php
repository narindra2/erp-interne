@php
    // $seleteds_ids =  $item->suivi  ? $item->suivi->types->implode("id",",") : "";
    // $seleteds_names = $item->suivi  ? $item->suivi->types->implode("name",",") : "";
    $seleteds_ids =  $item->suivi  ? $item->suivi->points->implode("project_type_id",",") : "";
    // $seleteds_names = $item->suivi  ? $item->suivi->points->pluck("project_type.name")->implode(",") : "";
    $seleteds_names = $item->typesName;

@endphp
<div class="select_box w-200px">
    <select style="color:#181C32" class="form-select f form-select-sm form-select-transparent" title="{{ $seleteds_names }}" seleteds ="{{ $seleteds_ids }}" data-can-edit="{{  isset($item->suivi->id) ? "false" : "true"  }}"  autocomplete="off"  name="types[]" id="input-types" disabled = true data-hide-search="false" multipleSelect="true"  data-control="select">
        @if ($item->suivi && $seleteds_names)
            <option disabled selected >{{ str_limite( $seleteds_names, 20) }}</option>
        @else
            <option disabled  @if ($clone) selected @endif >--Types--</option>
        @endif

        @foreach ($types as $type)
            <option value="{{ $type->id }}" >{{ $type->name }}</option>
        @endforeach
    </select>
</div>
<style>
    .select_box select{
        background: none;
        background-color: #ffffff;
    }
</style>