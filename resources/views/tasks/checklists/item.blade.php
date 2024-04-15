<div class="row" id="checklist-item-{{ $checklist->id }}">
    <div title=" {{ $checklist->is_do ? 'Re-Fait ?' : "Fait ?"}}" class="mb-3 form-check  form-check-custom col-10 form-check-success form-check-solid form-check-sm">
        <button type="button" for="checklist-id-{{ $checklist->id }}" id="button-resolve-ticket-1" class="btn btn-sm btn-white ">
            <span class="indicator-label" id="indicator-label">
                <input style="border: 1px solid;  border-color: #50cd89 ;" class="form-check-input resolve-checklist-input" type="checkbox" id="checklist-id-{{ $checklist->id }}" data-id="{{ $checklist->id }}" @if ($checklist->is_do) checked @endif>
            </span>
        </button>
        <label class="form-check-label">
            @if ($checklist->is_do)
                <span><del>{{ $checklist->description }}</del></span>
            @else
                <span >{{ $checklist->description }}</span>
            @endif
        </label>
    </div>
    <button type="button" id="delete-checklist-btn-{{ $checklist->id }}"  data-id="{{ $checklist->id }}" class="delete-checklist-btn btn btn-icon btn-sm btn-light-danger  mr-2">
        @include('partials.general._button-indicator', 
                ['label' => '<i class="la la-trash-o "></i>',
                  'message' => 'En suppression ...',])
    </button>
</div>
