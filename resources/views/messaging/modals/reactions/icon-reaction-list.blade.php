@php
    $reactions = getIconsReaction();
@endphp
<div class="dropdown mx-3">
    <a href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-smile"></i>
    </a>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
        <span class="menu-item">
            <a href="#" class="menu-link">
                @foreach ($reactions as $reaction)
                    <span class="menu-icon make-reaction-to-message" data-bs-toggle="tooltip" data-bs-original-title="{{ $reaction->name }}" data-message_id="{{ $message->id }}" data-reaction_id="{{ $reaction->id }}">{!! $reaction->icon !!}</span>
                @endforeach
            </a>
        </span>
    </ul>
</div>