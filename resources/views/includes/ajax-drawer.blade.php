
<div id="ajax-drawer" class="bg-dark" data-kt-drawer="true" data-kt-drawer-name="activities"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', 'lg': '500px'}"
    data-kt-drawer-direction="end" data-kt-drawer-toggle="#ajax-drawer-button"
    data-kt-drawer-close="#ajax-drawer-close" style="width: 700px !important;">
    <div class="card w-100 rounded-0">
        <div class="card-header bgi-position-y-bottom bgi-position-x-end bgi-size-cover bgi-no-repeat rounded-0 border-0 py-4" id="ajax-drawer-header" style="background-image:url('{{ asset("custom-css/header-bg.jpg") }}')">
            <h3 class="card-title fs-3 fw-bold text-white flex-column m-0" id="ajax-drawer-title"></h3>
            <div class="card-toolbar">
                <div  id="ajax-drawer-info"></div>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="ajax-drawer-close">
                    {!! theme()->getSvgIcon('icons/duotune/arrows/arr061.svg', 'svg-icon-1') !!}
                </button>
            </div>
        </div>
        <div class="position-relative" id="ajax-drawer-body">
            <div id="ajax-drawer-scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true"
                data-kt-scroll-height="auto" data-kt-scroll-wrappers="#ajax-drawer-body"
                data-kt-scroll-dependencies="#ajax-drawer-header, #ajax-drawer-footer" data-kt-scroll-offset="5px">
                <div id="ajax-drawer-content" class=""> </div>
            </div>
        </div>
    </div>
</div>
