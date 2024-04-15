@php
    $subject = get_template_info($notification,( $send_to ?? auth()->user() ));
@endphp
<div class="notification-item" data-notification-id="{{$notification->id}}" data-notification-is-unread ="{{ $notification->read_at ? 1 : 0 }}" >
<div class="separator my-2" ></div>
<a href="javascript:void(0)">
<div class="notice d-flex mb-1">
    <div class="d-flex flex-stack flex-grow-1 text-right" >
        <div class="fw-bold">
            <span id="notification-event" class="text-gray-{{ $notification->read_at ? "500" :"800" }} fw-bolder"># {{ get_array_value($subject,"title") ?? 'Action' }} </span><span class="badge badge-light-{{ get_array_value($subject,"class",null) ?? 'success' }} }} me-1"> {{ get_array_value($subject,"action") ?? 'Ajout' }}</span>
        </div>
        <div class="d-flex align-items-center mt-1 fs-6">
            <div class="text-muted me-2 fs-7"> <i>{{ $notification->created_at->diffForHumans() }}</i> </div>
        </div>
    </div>
</div>
<div class="text-muted fw-bold lh-lg mb-1">
    @if (get_array_value($subject,"profile"))
        <div class="symbol symbol-35px symbol-circle">
            {!! get_array_value($subject,"profile") !!}
        </div>
    @endif
    <span id="notification-desc" class="text-gray-{{ $notification->read_at ? "500" :"800" }}"> {!!  str_limite( get_array_value($subject,"sentence"),300)  !!}  </span>
</div>
</a>
</div>
@if (isset($from_load_more) && !$notification->read_at )
<script>
    $(document).ready(function() {
        (function ($) {
            let id = "{{$notification->id}}"
                        $.ajax({
                        url: url("/notification/set/seen"),
                        type: 'POST',
                        dataType: 'json',
                        data: {"_token" : _token ,"id" : id },
                        success: function(response) {
                            target.replaceWith(response.data)
                        },
                    });
        })(jQuery);
    })
</script>
@endif
