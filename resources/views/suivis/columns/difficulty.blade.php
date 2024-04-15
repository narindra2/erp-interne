<div class="select_box max-100w-px">
    <select class="form-select form-select-sm form-select-transparent row-detail-{{ $clone ? 0 : $item->id }}" id="difficulty-{{ $item->id ?? "0" }}" data-can-edit="true"  name="level_id"  disabled = "true" data-hide-search="true"  data-control="select2" >
        <option value="0"   @if (!$item->level_id) selected @endif >Diff.</option>
        @foreach ($levels as $level)
            <option value="{{ $level->id }}"   @if ($item->level_id == $level->id) selected @endif > {{ $level->level }} </option>
        @endforeach
    </select>
</div>
<style>
.select_box select{
    background: none;
    background-color: #ffffff;
}
</style>