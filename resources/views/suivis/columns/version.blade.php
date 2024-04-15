<div class="select_box w-150px">
    <select class="form-select form-select-sm  form-select-transparent version-input"  data-id="{{ $item->id ?? 0 }}"  data-can-edit=true  name="version_id"  id="input-version_id" disabled = true  >
    <option value="" disabled @if (!$item->version_id || !$clone )  @endif >--version--</option>
        @foreach ($versions as $version)
            <option value="{{ $version->id }}" @if ($version->id == $item->version_id && !$clone) selected @endif  >{{ $version->title }}</option>
        @endforeach
    </select>
</div>
<style>
    .select_box select{
        background: none;
        background-color: #ffffff;
    }
</style>