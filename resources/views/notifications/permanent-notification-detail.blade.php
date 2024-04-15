<div class="card-body" id="kt_help_body">
    <!--begin::Content-->
    <div id="kt_help_scroll" class="hover-scroll-overlay-y" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_help_body" data-kt-scroll-dependencies="#kt_help_header" data-kt-scroll-offset="5px" style="height: 915px;">
        @foreach ($notifications as $notification)
        <div class="d-flex align-items-center mb-7">
            <div class="d-flex flex-stack flex-grow-1 ms-4 ms-lg-6">
                <div class="d-flex flex-column me-2 me-lg-5">
                    <a href="/metronic8/demo1/../demo1/documentation/base/utilities.html" class="text-dark text-hover-primary fw-bolder fs-6 fs-lg-4 mb-1">
                        {{ get_array_value($notification,"title") }}
                    </a>
                    <div class="text-muted fw-bold fs-7 fs-lg-6">
                        {{ get_array_value($notification,"content") }}
                    </div>
                </div>
                <span class="svg-icon svg-icon-gray-400 svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                        <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
                    </svg>
                </span>
            </div>  
        </div>
        @endforeach
    </div>
</div>