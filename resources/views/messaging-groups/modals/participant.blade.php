<!--begin::User-->
<div class="row" id="participant-{{ $participant->id }}">
    <div class="col-1">
        <div class="symbol symbol-45px symbol-circle">
            <span class="symbol-label bg-light-danger text-primary fs-6 fw-bolder">@php echo $participant->user->sortname[0] @endphp</span>
        </div>
    </div>
    <div class="col-7 mt-3">
        <span class="fs-6 text-gray-800 me-2">{{ $participant->user->sortname }}</span>
    </div>
    @if ($action && auth()->user()->id != $participant->user->id)
        <div class="col-2">
            <span class="deleteParticipant" data-participant="{{ $participant->id }}"><button class="btn btn-light-danger btn-sm"><i class="fas fa-trash"></i></button></span>
        </div>
    @endif
</div>