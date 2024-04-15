{{-- <div class="count-user-reaction">{{ $message->reactions_count }}</div> --}}
@php
    $reactions = getIconsReaction();
@endphp
<div id="user-reaction-block-{{ $message->id }}">
    @foreach ($data as $id => $count)
        @include('messaging.modals.reactions.reaction-count', ['count' => $count, 'reaction' => $reactions->where('id', $id)->first(), 'message_id' => $message->id, 'title' => $message->getUserListWhoReact($id) ])
    @endforeach
</div>