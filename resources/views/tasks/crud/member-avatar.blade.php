<div class="symbol symbol-35px symbol-circle avatar-member" data-bs-toggle="tooltip"  data-bs-placement="bottom" title="{{ $user->sortname }}" aria-label="{{ $user->sortname }}" data-kt-initialized="1">
    <img alt="{{ $user->sortname }}" src="{{ $user->avatarUrl }}">
    @if ($user->deleted)
        <span title="{{ $user->sortname }} ne travaille plus dans notre société." class=" pointor symbol-badge badge badge-circle bg-danger start-100">x</span>
    @endif
</div>