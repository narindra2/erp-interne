<div class="select_box w-100px">
    <select class="form-select form-select-sm  form-select-transparent" data-can-edit=true  name="poles"  id="input-poles"   disabled = true data-hide-search="true"    >
        <option value=""  @if (!$item->poles && $clone) selected @endif >--poles--</option>
        @foreach ($poles as $pole)
            <option value="{{get_array_value($pole , "value")}}"   @if ($item->poles == get_array_value($pole , "value") && !$clone) selected @endif >#{{ get_array_value($pole , "value") }} </option>
        @endforeach
    </select>
</div>
<style>
.select_box select{
    background: none;
    background-color: #ffffff;
}
</style>
