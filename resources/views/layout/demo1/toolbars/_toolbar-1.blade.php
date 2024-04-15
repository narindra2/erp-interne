<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="{{ theme()->printHtmlClasses('toolbar-container', false) }} d-flex flex-stack">
        @if (theme()->getOption('layout', 'page-title/display') && theme()->getOption('layout', 'header/left') !== 'page-title')
            {{ theme()->getView('layout/page-title/_default') }}
        @endif
		<!--begin::Actions-->
        <div class="d-flex align-items-right ">
            <div class="me-4">
                @include('check.idle-timer')
            </div>
            <!--end::Wrapper-->
        </div>
		<!--end::Actions-->
    </div>
    <!--end::Container-->
</div>
<!--end::Toolbar-->
