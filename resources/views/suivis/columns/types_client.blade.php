@php
    $clt_type = ""; 
    if($item->suivi && isset($item->suivi->points) &&  isset($item->suivi->points[0])){
        $clt_type = $item->suivi->points[0]->client_type_id;
    }
@endphp
<div class="select_box w-150px">
    <select  class="form-select form-select-sm form-select-transparent" data-can-edit="{{  isset($item->suivi->id) ? "false" : "true"  }}" data-hide-search = "true" autocomplete="off"  name="type_client" id="input-type_client" disabled = true  multipleSelect="false"  >
        <option  disabled value="" >Type de client</option>
        @foreach ($types_client as $type)
            <option  @if($clt_type == $type->id) selected  @endif value="{{ $type->id }}" >{{ $type->name }}</option>
        @endforeach
    </select>
</div>
<style>
    .select_box select{
        background: none;
        background-color: #ffffff;
    }
</style>