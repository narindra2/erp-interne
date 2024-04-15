<div class="d-flex symbol-group symbol-hover">
    @foreach ($users as  $user)
        <div class="symbol symbol-30px symbol-circle " data-bs-placement="bottom" data-bs-toggle="tooltip"  data-bs-original-title="{{$user->sortname }}">
            <img alt="Pic" src="{{ $user->avatarUrl }}">
        </div>
    @endforeach
    @if ($showAddBtn)
        @php
            echo modal_anchor(url('/item-movements/modal/edit-user/' . $id), '<i class="text-hover-primary fas fa-user-plus fs-3"></i>', ['title' => 'Les utilisateurs', 'data-post-id' => 1]);
        @endphp
    @endif
</div>
<script>
    $(document).ready(function() {
        KTApp.initBootstrapTooltips();
    })
</script>