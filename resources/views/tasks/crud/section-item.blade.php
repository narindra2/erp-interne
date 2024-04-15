<li class="nav-item nav-item-section-task" role="presentation" id="section-id-{{ $section->id  }}">
    <a  class="nav-link task-section btn btn-sm btn-color-muted position-relative btn-active btn-active-primary fw-bold px-4 me-1 "
        data-kt-timeline-widget-1="tab" data-bs-toggle="tab"
        data-task-section-id="{{ $section->id }}" 
        aria-selected="true" role="tab">
        @if ($for_user->id == $section->creator_id)
            <span id="section-title-{{ $section->id }}" ><u>{{ $section->title }} </u></span>
        @else
            <span id="section-title-{{ $section->id }}" >{{ $section->title }}</span>
        @endif
        <span id="alert-section-{{ $section->id }}" class="position-absolute top-0 start-100 translate-middle badge badge-sm badge-circle badge-danger d-none">!</span>
    </a>
    @php
        echo modal_anchor(url('/task/create/section/modal'), "Editer section", [ "id" =>"modal-form-edit-$section->id", 'title' => "Editer section", 'class' => 'd-none', 'data-modal-lg' => true,'data-post-section_id' => $section->id]);
    @endphp
</li>
