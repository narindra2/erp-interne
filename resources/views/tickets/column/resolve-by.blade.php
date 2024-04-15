@if ($ticket->resolve_by)
    <div class="d-flex symbol-group symbol-hover">
        <div class="symbol symbol-30px symbol-circle " data-bs-placement="bottom" data-bs-toggle="tooltip"  data-bs-original-title="{{ $ticket->resolver->sortname }}">
            <img alt="Pic" src="{{ $ticket->resolver->avatarUrl }}">
        </div>
    </div>
<script>
    $(document).ready(function() {
        KTApp.initBootstrapTooltips();
    })
</script>
@else
    -
@endif


