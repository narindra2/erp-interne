@php
    $color_ribbon = $task->ribbon;
@endphp
@if ($color_ribbon)
    <div class="ribbon ribbon-triangle bottom-0 ribbon-bottom-start border-custom-{{ $task->id }}" style="border-radius: 0px 8px "></div> 
@endif

<div class="flex-column" title="{{  $task->title }}">
    <span class=" fw-bold align-items-end ">
        @php
            $label = $task->label_info();
        @endphp
        @if ($label)
            <span class="position-absolute top-0  translate-middle mr-8 badge rounded-pill badge-sm badge-{{ $label['class'] }}" style="left: 88% !important;">{{ $label['text'] }}</span>
        @endif
        {!! $task->get_class_deadline() !!}
        {{ str_limite( $task->title , 28 , "...") }}
        
        <span data-kt-indicator="off" id="indicator-id-{{ $task->id }}">
            <span class="indicator-progress">... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
        </span>
    </span>
    
</div>
<div class="d-flex flex-column align-items-end ">
    @if ($task->assign_to)
        <div class="symbol-group symbol-hover ">
            @foreach ($task->responsibles as $user)
                <div class="symbol symbol-20px symbol-circle mb-1" data-bs-toggle="tooltip" title="{{ $user->sortname }}" data-bs-original-title="{{ $user->sortname }}">
                    <img alt="Pic" src="{{ $user->avatarUrl }}">
                </div>
            @endforeach 
        </div>
    @endif
    <span > <span class="mt-2 badge badge-light-dark"><u>Crée par</u> : {{ $task->autor->sortname }}</span> 
        @if ($task->recurring)
            <span class="mt-2 badge badge-light-dark" title="Tâche recurente">
                <i class="fas fa-retweet"></i>
            </span>
        @endif
     </span>
    
    <div style="display:none">
        @php
            echo modal_anchor(url('/task/detail'), '' , ['title' => trans('lang.update-detail-task')  , "id" => "detail-btn-$task->id" ,"data-post-task_id" => $task->id ,"data-modal-lg" => true]);
        @endphp
    </div>
</div>
@if ($color_ribbon )
<style>
    .border-custom-{{ $task->id }} {
        border-color: {{ $color_ribbon }} !important;
    } 
    </style>
@endif
