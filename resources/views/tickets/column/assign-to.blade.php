<div class="d-flex symbol-group symbol-hover">
    @foreach ($responsibles as  $user)
        <div class="symbol symbol-30px symbol-circle " data-bs-placement="bottom" data-bs-toggle="tooltip"  data-bs-original-title="{{$user['name'] ?? $user->name }}">
            <img alt="Pic" src="{{ $user->avatarUrl }}">
        </div>
    @endforeach
    @if (isset($add) && ($for_user->isIt() || $for_user->isAdmin())) 
        {!! $add !!}
    @endif
</div>
<script>
    $(document).ready(function() {
        KTApp.initBootstrapTooltips();
    })
</script>