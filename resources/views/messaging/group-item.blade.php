<div id="group-{{ $group->id }}">
    <!--begin::User-->
    <div class="d-flex flex-stack py-4">
        <!--begin::Details-->
        <div class="d-flex align-items-center">
            <!--begin::Avatar-->
            <div class="symbol symbol-45px symbol-circle">
                <span class="symbol-label bg-light-danger text-primary fs-6 fw-bolder">{{ $group->messageGroup->getFirstLetter() }}</span>
            </div>
            <!--end::Avatar-->
            <!--begin::Details-->
            <div class="ms-5">
                <a href="#messageContent" class="fs-5 fw-bolder text-gray-900 text-hover-primary mb-2" 
                    data-toggle="ajax-tab" data-bs-toggle="tab"
                    data-load-url="{{ url("/messaging/discussion-group/{$group->messageGroup->id}")  }}">{{ $group->messageGroup->name }}
                </a>
            </div>
            <!--end::Details-->
        </div>
        <!--end::Details-->
    </div>
    <!--end::User-->
    <!--begin::Separator-->
    <div class="separator separator-dashed d-none"></div>
    <!--end::Separator-->
</div>
