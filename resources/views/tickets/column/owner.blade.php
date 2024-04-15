<div class="d-flex align-items-center">
    <div class="symbol symbol-30px symbol-circle">
        <img alt="Pic" src="{{ $owner->avatarUrl }}"> &nbsp;
    </div>
    <div class=" d-flex">
        <span>  {{ $owner->sortname }}  </span>
        @if ($owner->isAdmin()) 
            <span class="badge badge-light-info">Admin</span>
        @endif
    </div>
</div>