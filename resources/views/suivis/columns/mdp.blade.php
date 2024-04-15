<div class="select_box w-200px">
    <select class="form-select form-select-sm  form-select-transparent" data-can-edit=true  name="follower" id="input-follower"  disabled = true    >
        <option value=""  @if (!$item->follower && $clone) selected @endif disabled >--M2p--</option>
        @foreach ($mdp as $user)
            <option value="{{get_array_value($user , "value")}}"   @if ( $item->follower == get_array_value($user , "value") && !$clone) selected @endif > {{ get_array_value($user , "text") }} </option>
        @endforeach
    </select>
</div>
<style>
.select_box select{
    background: none;
    background-color: #ffffff;
}
</style>
