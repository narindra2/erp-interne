<div class="timeline-item align-items-center mb-4 task-comment-item" id="task-comment-item-{{ $comment->id }}">
    <div class="timeline-line w-20px mt-9 mb-n14"></div>
    <div class="timeline-content m-0">
        <span href="#" class="fs-6 text-gray-800 fw-bolder d-block " id="task-comment-item-content-{{ $comment->id }}" >{{ $comment->content }}</span>
        <i class="fas fa-angle-double-right"></i><span class="fw-bold text-gray-400"> <i> <u>Ajouté par</u> : {{ $comment->creator->sortname }}, {{  $comment->created_at->isToday() ? $comment->created_at->diffForHumans() :  $comment->created_at->format("d-m-Y H:m") }} </i> </span>
        @if ($comment->user_id == auth()->id())
        <span class="action-hover-hide">
            [
                <a href="#" class="fw-bold text-gray-400 delete-task-comment" data-comment-id="{{ $comment->id }}"> Supprimer | </a>
                <a href="#" class="fw-bold text-gray-400 edit-task-comment" data-comment-id="{{ $comment->id }}"> Modifier </a>
            ]
        </span>
        @endif
    </div>
</div>
