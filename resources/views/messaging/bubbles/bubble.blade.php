@php
    $nb_messages_not_seen = isset($count_messages_not_seen) ? $count_messages_not_seen : $message->count;
@endphp
<span class="bubble-go-to-conversation chat-modal" id="chat-modal-{{ $message->sender->id }}" data-id="{{ $message->sender_id }}">
    <div class="symbol symbol-35px symbol-circle" 
        data-bs-placement="bottom"
        data-bs-toggle="tooltip" 
        data-bs-original-title="{{ $message->sender->sortname }}"
        style="transform: rotate(279deg)">
        <img alt="Pic" src="{{ $message->sender->avatar_url }}">
        <span id="chat-private-id-notification-count-{{ $message->sender->id }}" class="position-absolute top-0 start-100 translate-middle  badge badge-circle  badge-sm badge-light-danger">{{ $nb_messages_not_seen }}</span>
        {{-- @if($user->message_not_seen)
            <span id="chat-private-id-notfication-count-{{ $user->id }}"  class="position-absolute top-0 start-100 translate-middle  badge badge-circle  badge-sm badge-light-info ">{{$user->message_not_seen}}</span>
        @else
            <span id="chat-private-id-notfication-count-{{ $user->id }}" style="display: none" class="position-absolute top-0 start-100 translate-middle  badge badge-circle  badge-sm badge-light-info "></span>
        @endif --}}
    </div>
</span>