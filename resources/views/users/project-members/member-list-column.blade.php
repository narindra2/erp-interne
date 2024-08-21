<div class="symbol-group symbol-hover mb-3">
    @php
        $users = isset($validator_dayoff) ? $project->dayoffValidator :  $project->members;
    @endphp
    @foreach ($users as $user)
        <div class="symbol symbol-35px symbol-circle" title="{{ $user->sortname }}" data-bs-toggle="tooltip" aria-label="{{ $user->sortname }}" data-bs-original-title="{{ $user->sortname }}" data-kt-initialized="1">
            <img alt="Pic" src="{{ $user->avatarUrl }}">
            @if ($user->deleted)
                <span title="{{ $user->sortname }} ne travaille plus dans notre société." style="cursor: pointer" class="symbol-badge badge badge-circle bg-danger start-100">x</span>
            @endif
        </div>
    @endforeach
    @php
    if (isset($validator_dayoff) && $validator_dayoff) {
        echo modal_anchor(url('/project/add/validator-dayoff'), ' <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold" >+</span>', ['title' => "Ajouter des validateurs de congé dans $project->name","data-post-id" => $project->id, "data-modal-lg" => true, 'class' => 'symbol symbol-35px symbol-circle']);
    }else {
        echo modal_anchor(url('/project/add/members'), ' <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold" >+</span>', ['title' => "Ajouter des membres  dans $project->name","data-post-id" => $project->id, "data-modal-lg" => true, 'class' => 'symbol symbol-35px symbol-circle']);
    }
    @endphp
</div>