
@php
$class = "";
 foreach ($status as $s){
    if ($item->status_id == get_array_value($s , "value")) {
        $class = get_array_value($s , "class");
    }
 };
@endphp
<div  class="select_box w-100px bg-active-{{ $class }} text-active-{{ $class }} active edit-item-part " data-id="{{ $item->id }}" data-can-edit=true>
<select class="form-select form-select-sm   form-select-transparent edit-item-part"  data-id="{{ $item->id }}" data-can-edit=true  name="status_id" id="input-status_id" disabled = "true" data-hide-search="true"   >
    @foreach ($status as $st)
        <option value="{{get_array_value($st , "value")}}"   @if ($item->status_id == get_array_value($st , "value") && !$clone) selected @endif >{{ get_array_value($st , "text") }}</option>
    @endforeach
</select>
</div>
<style>
.select_box select{
    background: none;
    background-color: #ffffff;
}
</style>
