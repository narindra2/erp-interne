    {{-- @if (!$clone)
    <div class="d-flex align-items-center">
        <div class="symbol symbol-30px symbol-circle">
            <img alt="Pic" src="{{ $item->user->avatarUrl }}" title="{{ $item->user->sortname }} "> &nbsp;
        </div>
        <div class=" d-flex">
            <span> {{ $item->user->sortname }}  </span>
        </div>
    </div> 
    @endif --}}

    <div class="select_box w-200px">

        <select class="form-select form-select-sm form-select-transparent" 
            {{-- title="{{ $item->user ? $item->user->sortname : "" }}" --}}
            id="input-user_id" 
            {{-- data-can-edit="{{ isset($item->suivi->id) ? 'false' : 'true' }}" --}}
            data-can-edit="true"
            autocomplete="off" 
            name="user_id" 
            disabled=true 
            data-hide-search="false"
            data-ajax--url = {{ url("/search/user") }}
            data-ajax--cache = "true"
            data-minimum-input-length = "2"
            data-formatResult='optionFormat'
            data-formatSelection='optionFormat'>
            <option  selected value=""> Assigné(e) à </option>
            @if ($item->user &&  $item->suivi && !$clone)
                <option  selected value="{{ $item->user->id }}">{{ str_limite($item->user->sortname, 20) }}</option>
            @endif
        </select>
    </div>
    <style>
        .select_box select {
            background: none;
            background-color: #ffffff;
        }
    </style>
