<div class="d-flex flex-stack py-4">
    <!--begin::Details-->
    <div class="d-flex align-items-center discussionGroupModal" data-id="{{ $group->id }}">
        <!--begin::Avatar-->
        <div class="symbol symbol-45px symbol-circle">
            <span class="symbol-label bg-light-danger text-danger fs-6 fw-bolder">@php echo $group->name[0] @endphp</span>
        </div>
        <!--end::Avatar-->
        <!--begin::Details-->
        <div class="ms-5">
            <h5>{{ $group->name }}</h5>
            {{-- <div class="fw-bold text-muted">{{ $contact->content }}</div> --}}
        </div>
        <!--end::Details-->
    </div>
    <!--end::Details-->
</div>
<!--end::User-->
<!--begin::Separator-->
<div class="separator separator-dashed d-none"></div>
<!--end::Separator-->