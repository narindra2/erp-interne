@if (strlen($desc) > 40)
    <span data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="bottom" title="{{ $desc }}">
        {{ str_limite($desc, 40, ' ... ') }}
    </span>
@else
      {{ $desc }}
@endif
