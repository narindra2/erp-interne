<span class="mx-1 count-reaction"
    data-message_id={{ $message_id }} data-reaction_id={{ $reaction->id }} data-bs-html="true" data-trigger="focus"
    data-bs-toggle="tooltip" data-bs-placement="top" title="{!! $title !!}">
    {!! $reaction->icon !!} 
    <span id="user-reaction-{{ $message_id }}-{{ $reaction->id }}">{{ $count }}</span>
</span>