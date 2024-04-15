<div id="contact-{{ $contact->contact_id }}">
    <!--begin::User-->
    <div class="d-flex flex-stack py-4">
        <!--begin::Details-->
        <div class="d-flex align-items-center">
            <!--begin::Avatar-->
            <div class="symbol symbol-45px symbol-circle">
                <span class="symbol-label bg-light-danger text-danger fs-6 fw-bolder">M</span>
            </div>
            <!--end::Avatar-->
            <!--begin::Details-->
            <div class="ms-5">
                @include('messaging.link-to-discussion', ['contact' => $contact])
                <div class="fw-bold text-muted">{{ $contact->content }}</div>
            </div>
            <!--end::Details-->
        </div>
        <!--end::Details-->
        <!--begin::Lat seen-->
        <div class="d-flex flex-column align-items-end ms-2">
            <span class="text-muted fs-7 mb-1">{{ getDateHuman($contact->created_at) }}</span>
        </div>
        <!--end::Lat seen-->
    </div>
    <!--end::User-->
    <!--begin::Separator-->
    <div class="separator separator-dashed d-none"></div>
    <!--end::Separator-->
</div>
