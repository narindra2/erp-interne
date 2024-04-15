@foreach ($status as $st)
    @if ($item->status_id == get_array_value($st , "value"))
        {{-- <div id="class-row-color-{{ $item->id }}" data-row-color="bg-{{  get_array_value($st , "class") }}" class="text-inverse-primary p-3 h-40px fw-semibold fw-6 bg-{{ get_array_value($st , "class")}}">{{ get_array_value($st , "group")}}</div> --}}
        <div id="class-row-color" data-row-color="bg-{{  get_array_value($st , "class") }}" class="text-inverse-primary p-3 h-40px fw-semibold fw-6 bg-{{ get_array_value($st , "class")}}">{{ get_array_value($st , "group")}}</div>
    @endif
@endforeach