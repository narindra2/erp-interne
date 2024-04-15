<div class="dropdown mx-3">
    <a href="#" role="button" id="dropdownMenuLink"
        data-bs-toggle="dropdown" aria-expanded="false">
        ...
    </a>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
        <li>
            @php
                echo modal_anchor(url("/messaging/users-list-seen-message-modal/$message->id"), '<i class="bi bi-eye-fill mr-2"></i> Vue</a>', ["title" => "Les personnes ayant vu le message", "class"=> "dropdown-item"]);
            @endphp
        </li>
        {{-- @if ($message->isMyMessage())
            <li><a class="dropdown-item" href="#"><i class="fas fa-trash"></i> Supprimer</a></li>
        @endif     --}}
    </ul>
</div>