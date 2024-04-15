<form class="form"  method="POST" action="/">
    <div class="card-body ">
            <div class="alert alert-dismissible bg-light-danger d-flex flex-center flex-column py-10 px-10 px-lg-20 mb-10">
                <span class="svg-icon svg-icon-5tx svg-icon-danger mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"></rect>
                        <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black"></rect>
                        <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black"></rect>
                    </svg>
                </span>
                <div class="text-center text-dark">
                    <h1 class="fw-bolder mb-5">@lang('lang.ticket_resolved')</h1>
                    <div class="separator separator-dashed border-danger opacity-25 mb-5"></div>
                    <div class="mb-9"></strong>.
                </div>
            </div>
        </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
    </div>
</form>
