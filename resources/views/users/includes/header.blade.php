<div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10" style="background-size: auto calc(100% + 10rem); background-position-x: 100%">
    <!--begin::Card header-->
    <div class="card-header border-1 pt-1">
        <div class="me-2 card-title align-items-start flex-column">

            <div class="text-muted fs-7 fw-bold">
                <div class="d-flex align-items-center">
                    <!--begin::Icon-->
                    <div class="symbol symbol-circle me-5">
                        <div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <!--end::Icon-->
                    <!--begin::Title-->
                    <div class="d-flex flex-column">
                        @if(!empty($user->id))
                            <h2 class="mb-1">@lang('lang.edit')</h2>
                        @else
                            <h2 class="mb-1">@lang('lang.new-collab')</h2>
                        @endif
                    </div>
                    <!--end::Title-->
                </div>
            </div>
        </div>
        <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
            <a href="{{ url('/users') }}" class="btn btn-sm btn-light-primary">Liste des employés</a>
        </div>
    </div>

    <!--end::Card header-->
</div>
