@php
    $count = isset($count) ? $count : $messageGroup->count;
    $id = isset($id) ? $id : $messageGroup->group->id;
    $name = isset($name) ? $name : $messageGroup->group->name;
@endphp
<span class="bubble-go-to-conversation chat-group-modal" id="chat-group-modal-{{ $id }}" data-id="{{ $id }}">
    <div class="symbol symbol-35px symbol-circle" data-bs-placement="bottom" data-bs-toggle="tooltip" data-bs-original-title="{{ $name }}" style="transform: rotate(279deg)">
        <span class="symbol-label bg-light-danger text-danger fs-6 fw-bolder">{{ strtoupper($name[0]) }}</span>
        <span id="chat-private-id-notification-group-count-{{ $id }}" class="position-absolute top-0 start-100 translate-middle  badge badge-circle  badge-sm badge-light-danger">{{ $count }}</span>
    </div>
</span>