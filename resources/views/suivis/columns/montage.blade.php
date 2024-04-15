
<div class="select_box ">
    <select class="form-select form-select-sm form-select-transparent" data-can-edit=true  name="montage" id="input-montage"  disabled = true data-hide-search="true"    >
        <option value=""disabled  @if (!$item->montage && $clone) selected @endif >Mont.</option>
        @foreach ($montages as $montage)
            <option value="{{get_array_value($montage , "value")}}"   @if ( $item->montage == get_array_value($montage , "value") && !$clone) selected @endif > {{ get_array_value($montage , "value") }} </option>
        @endforeach
    </select>
</div>
<style>
.select_box select{
    background: none;
    background-color: #ffffff;
}
</style>
