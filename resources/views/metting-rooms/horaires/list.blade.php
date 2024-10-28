{{-- <label class="form-label require"> Horaires {{ now()->format("H:i:s") }} : </label> --}}
<label class="form-label require"> Horaires : </label>
@forelse ($horaires as $horaire)
{{-- <div class="d-flex align-items-center mb-4 {{ !$horaire->is_in_progress() ? "need-pading" : "" }}"  > --}}
<div class="d-flex align-items-center mb-4 "  >
    <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4 bg-{{ $horaire->status["class"] }}"></span>
     <div class="flex-grow-1 me-5">
         <div class="text-gray-800 fw-semibold fs-{{ !$horaire->is_in_progress() ? "5" : "2" }}">
             @if ( $horaire->is_past() && !$horaire->is_in_progress())
                 <del>{{ $horaire->start }} - {{ $horaire->end }}</del>
             @else
                 {{ $horaire->start }} - {{ $horaire->end }} 
                 @if ( $horaire->is_in_progress())
                    <span class="spinner-grow text-success mt-3" role="status">
                        <span class="sr-only">Loading...</span>
                    </span>
                  @endif
             @endif
         </div>
         <div class="text-gray-700 fw-semibold fs-6"> <u>Type</u>: {{ $horaire->type}}</div>
         @if ($horaire->subject)
         <div class="text-gray-700 fw-semibold fs-6"> <u>Sujet</u>: {{ $horaire->subject }}</div>
         @endif
         @if ($multiple_select)
            <div class="text-gray-700 fw-semibold fs-6"> <u>Date réunion</u>: {{ $horaire->day_meeting }}</div>
         @endif
         <div class="text-gray-400 fw-semibold fs-7">crée par :
         <a href="#" class="text-primary opacity-75-hover fw-semibold"> 
            {{ $horaire->creator->sortname }}</a>, 
            Statut : <span class="badge badge-light-{{ $horaire->status["class"] }}">{{ $horaire->status["text"] }} </span>
           
         </div>
     </div>
     {{-- @if (!$horaire->is_past() && auth()->id() == $horaire->creator_id) --}}
     @if ( auth()->id() == $horaire->creator_id)
         @php
            echo modal_anchor(url('/meeting-room/create-meeting/modal-form'), "Editer" , [
                'title' => ' Modifier l\'horaire ',
                'data-modal-lg' => true,
                'data-post-room_id' => $horaire->room_id,
                'data-post-horaire_id' => $horaire->id,
                'class' => 'btn btn-sm btn-light ',
            ]);
        @endphp
     @endif
     </div>
@empty
    <div class="d-flex align-items-center mb-4 "  >
        <span > <i> - Pas de horaires </i></span>
    </div>
@endforelse
<style>

    .need-pading {
        padding-left: 10px;
    }
</style>