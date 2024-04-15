
<div class="select_box w-150px">
    <select class="form-select form-select-sm form-select-transparent row-detail-{{ $clone ? 0 : $item->id }}" data-can-edit=true  name="category" id="input-category" disabled = true data-hide-search="true"    >
        <option value=""disabled  @if (!$item->cat && $clone) selected @endif >Type de dossier</option>
        @foreach ($cats as $cat)
            <option value="{{get_array_value($cat , "value")}}"   @if (!$clone && $item->suivi->category == get_array_value($cat , "value") ) selected @endif > {{ get_array_value($cat , "text") }} </option>
        @endforeach
    </select>
</div>
<style>
.select_box select{
    background: none;
    background-color: #ffffff;
}
</style>
