<div class="symbol-group symbol-hover mb-3">
    @foreach ($project->members as $user)
        <div class="symbol symbol-35px symbol-circle" title="{{ $user->sortname }}" data-bs-toggle="tooltip" aria-label="{{ $user->sortname }}" data-bs-original-title="{{ $user->sortname }}" data-kt-initialized="1">
            <img alt="Pic" src="{{ $user->avatarUrl }}">
            @if ($user->deleted)
                <span class="symbol-badge badge badge-circle bg-danger start-100">!</span>
            @endif
        </div>
    @endforeach
    @php
        // $count = $project->members->count();
    @endphp
   
    @php
       echo modal_anchor(url('/project/add/members'), ' <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold" >+</span>', ['title' => "Ajouter des membres dans ce projet : $project->name","data-post-id" => $project->id, "data-modal-lg" => true, 'class' => 'symbol symbol-35px symbol-circle']);
    @endphp
</div>