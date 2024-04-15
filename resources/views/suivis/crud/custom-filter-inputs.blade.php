<div class="separator mb-2"></div>
<div class="row">
    @if (auth()->user()->isM2pOrAdmin() ||auth()->user()->isCp())
    <div class="col-md-4 mb-4">
        <label for="user_ids" class="form-label">@lang('lang.users')</label>
        <select name="users[]" id="user_ids" class="form-select form-select-solid select2-dropdown rounded-start-0 border-start"
            multiple="multiple"
            data-control="select2"
            data-hide-search="true" data-placeholder="Rechercher ... "
            data-dropdown-parent={{ isset($modal) ? "#ajax-modal" : "#dropdown-filter-custom"}}
            data-allow-clear="true"
            data-hide-search="true" 
            data-ajax--url = {{ url("/search/user") }}
            data-ajax--cache = "true"
            data-minimum-input-length = "2" >
            <option value="0" disabled>@lang('lang.users')</option>
        </select>
    </div>
    @endif

    <div class="col-md-4 mb-4">
        <label for="folder_ids" class="form-label">@lang('lang.folders')</label>
        <select name="suivis[]" id="folder_ids" class="form-select form-select-solid select2-dropdown rounded-start-0 border-start" 
            multiple="multiple" 
            data-control="select2"
            data-hide-search="true" 
            data-allow-clear="true"
            data-dropdown-parent={{ isset($modal) ? "#ajax-modal" : "#dropdown-filter-custom"}}
            data-ajax--url = {{ url("/search/folder") }}
            data-ajax--cache = "true"
            data-minimum-input-length = "2"
            data-placeholder="Rechercher ... ">
            <option value="0" disabled>@lang('lang.folders')</option>
        </select>
    </div>
    <div class="col-md-4 mb-4">
        @php
            $types_projet = get_array_value($options,"types");
        @endphp
        <label for="type_project" class="form-label">@lang('lang.type_project')</label>
        <select  name="types[]" id="type_project" class="form-select form-select-solid select2-dropdown rounded-start-0 border-start" multiple="multiple"
        data-dropdown-parent={{ isset($modal) ? "#ajax-modal" : "#dropdown-filter-custom"}} data-control="select2" data-hide-search="true"  data-placeholder="Selectionner ... ">

            <option value="0" disabled>@lang('lang.type_project')</option>
            @foreach ($types_projet as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>
    @if (auth()->user()->isADessignator())
        <div class="col-md-4 mb-4">
            <label for="pole_ids" class="form-label">@lang('lang.poles')</label>
            <select name="poles[]" id="pole_ids" class="form-select form-select-solid select2-dropdown rounded-start-0 border-start" multiple="multiple"
                data-control="select2" data-allow-clear="true" data-dropdown-parent={{ isset($modal) ? "#ajax-modal" : "#dropdown-filter-custom"}} data-hide-search="true" data-placeholder="Selectionner ... ">
                <option value="0" disabled>@lang('lang.poles')</option>
                @php
                    $poles = get_array_value($options , "poles");
                @endphp
                @foreach ($poles as $pole)
                    <option value="{{ get_array_value($pole , "value") }}">{{ get_array_value($pole , "text") }}</option>
                @endforeach
            </select>
        </div>
    @endif
</div>
<div class="row">
    <div class="col-md-4 mb-4">
        <label for="version_ids" class="form-label">@lang('lang.version')</label>
        <select name="versions[]" id="version_ids" class="form-select form-select-solid select2-dropdown rounded-start-0 border-start" multiple="multiple"
            data-control="select2" data-allow-clear="true" data-hide-search="true" data-dropdown-parent={{ isset($modal) ? "#ajax-modal" : "#dropdown-filter-custom"}} data-placeholder="Selectionner ... ">
            <option value="0" disabled >@lang('lang.version')</option>
            @php
                $versions = get_array_value($options , "versions");
            @endphp
            @foreach ($versions as $version)
                <option value="{{ $version->id }}">{{$version->title }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-4">
        <label for="montage_ids" class="form-label">@lang('lang.montage')</label>
        <select name="montages[]" id="montage_ids" class="form-select form-select-solid select2-dropdown rounded-start-0 border-start" multiple="multiple"
            data-control="select2" data-allow-clear="true" data-dropdown-parent={{ isset($modal) ? "#ajax-modal" : "#dropdown-filter-custom"}} data-hide-search="true" data-placeholder="Selectionner ... ">
            <option value="0" disabled>@lang('lang.montage')</option>
            @php
                $montages = get_array_value($options , "montages");
            @endphp
            @foreach ($montages as $montage)
                <option value="{{ get_array_value($montage , "value") }}">{{ get_array_value($montage , "text") }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-4">
        <label for="status_ids" class="form-label">@lang('lang.status')</label>
        <select name="status[]" id="status_ids" class="form-select form-select-solid select2-dropdown rounded-start-0 border-start" multiple="multiple"
            data-control="select2" data-allow-clear="true"  data-dropdown-parent={{ isset($modal) ? "#ajax-modal" : "#dropdown-filter-custom"}} data-hide-search="true" data-placeholder="Selectionner ... ">
            <option value="0" disabled >@lang('lang.status')</option>
            @php
                $status = get_array_value($options , "status");
            @endphp
            @foreach ($status as $s)
                <option value="{{ get_array_value($s , "value") }}">{{ get_array_value($s , "text") }}</option>
            @endforeach
        </select>
    </div>
</div>
@if (!auth()->user()->isADessignator())
<div class="row">
    <div class="col-md-4 mb-4">
        <label for="poles_ids" class="form-label">@lang('lang.poles')</label>
        <select name="poles[]" id="poles_ids" class="form-select form-select-solid select2-dropdown rounded-start-0 border-start" multiple="multiple"
            data-control="select2" data-allow-clear="true" data-dropdown-parent={{ isset($modal) ? "#ajax-modal" : "#dropdown-filter-custom"}} data-hide-search="true" data-placeholder="Selectionner ... ">
            <option value="0" disabled>@lang('lang.poles')</option>
            @php
                $poles = get_array_value($options , "poles");
            @endphp
            @foreach ($poles as $pole)
                <option value="{{ get_array_value($pole , "value") }}">{{ get_array_value($pole , "text") }}</option>
            @endforeach
        </select>
    </div>
    
</div>
@endif
