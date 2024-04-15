<span class="deleteParticipant" data-participant_id="{{ $participant->id }}">
    <button class="btn btn-light-danger btn-sm">
        @include('partials.general._button-indicator', ['label' => '<i class="fas fa-trash"></i>', "message" => trans('lang.sending')])
    </button>
    {{-- <button class="btn btn-light-danger btn-sm">
        <i class="fas fa-trash"></i>
    </button> --}}
</span>
