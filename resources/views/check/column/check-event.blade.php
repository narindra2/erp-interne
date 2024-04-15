@php
    $events_class = [
        "fin" => "danger",
        "in" => "success",
        "out" => "primary",
        "pause" => "warning",
    ]
@endphp
<span class="badge badge-light-{{ get_array_value($events_class, $check->check_event)}}"> @lang("lang.$check->check_event") </span>
