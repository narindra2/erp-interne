<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"{!! theme()->printHtmlAttributes('html') !!} {{ theme()->printHtmlClasses('html') }}>
{{-- begin::Head --}}
<head>
    <meta charset="utf-8"/>
    <title>ERP | Thirty-One Agency</title>
    {{-- <meta name="description" content="{{ ucfirst(theme()->getOption('meta', 'description')) }}"/>
    <meta name="keywords" content="{{ theme()->getOption('meta', 'keywords') }}"/>
    <link rel="canonical" href="{{ ucfirst(theme()->getOption('meta', 'canonical')) }}"/> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    {{-- <link rel="shortcut icon" href="{{ asset(theme()->getDemo() . '/' .theme()->getOption('assets', 'favicon')) }}"/> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('dynamic_link')
    @if (auth()->user())
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
       {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css"> --}}
        <link href="{{ asset('demo1/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
       
    @endif
    {{-- begin::Fonts --}}
    {{ theme()->includeFonts() }}
    {{-- end::Fonts --}}

    @if (theme()->hasOption('page', 'assets/vendors/css'))
        {{-- begin::Page Vendor Stylesheets(used by this page) --}}
        @foreach (array_unique(theme()->getOption('page', 'assets/vendors/css')) as $file)
            {!! preloadCss(assetCustom($file)) !!}
        @endforeach
        {{-- end::Page Vendor Stylesheets --}}
    @endif

    @if (theme()->hasOption('page', 'assets/custom/css'))
        {{-- begin::Page Custom Stylesheets(used by this page) --}}
        @foreach (array_unique(theme()->getOption('page', 'assets/custom/css')) as $file)
            {!! preloadCss(assetCustom($file)) !!}
        @endforeach
        {{-- end::Page Custom Stylesheets --}}
    @endif

    @if (theme()->hasOption('assets', 'css'))
        {{-- begin::Global Stylesheets Bundle(used by all pages) --}}
        @foreach (array_unique(theme()->getOption('assets', 'css')) as $file)
            @if (strpos($file, 'plugins') !== false)
                {!! preloadCss(assetCustom($file)) !!}
            @else
                <link href="{{ assetCustom($file) }}" rel="stylesheet" type="text/css"/>
            @endif
        @endforeach
        {{-- end::Global Stylesheets Bundle --}}
    @endif

    @if (theme()->getViewMode() === 'preview')
        {{ theme()->getView('partials/trackers/_ga-general') }}
        {{ theme()->getView('partials/trackers/_ga-tag-manager-for-head') }}
    @endif

    @yield('styles')

    <link href="{{ asset('library/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
    <link href="{{ asset('library/bootstrap-fileinput/themes/explorer/theme.min.css') }}" rel="stylesheet">
</head>
{{-- end::Head --}}

{{-- begin::Body --}}
<body {!! theme()->printHtmlAttributes('body') !!} {!! theme()->printHtmlClasses('body') !!} {!! theme()->printCssVariables('body') !!}>

@if (theme()->getOption('layout', 'loader/display') === true)
    {{ theme()->getView('layout/_loader') }}
@endif

@yield('content')
<script>
    window.authUser = {
        id : {{ optional( auth()->user() )->id ?? 0 }}
    }
</script>
{{-- begin::Javascript --}}

    @if (theme()->hasOption('assets', 'js'))
        {{-- begin::Global Javascript Bundle(used by all pages) --}}
        @foreach (array_unique(theme()->getOption('assets', 'js')) as $file)
            <script src="{{ asset(theme()->getDemo() . '/' .$file) }}"></script>
        @endforeach
        {{-- end::Global Javascript Bundle --}}
    @endif

@if (theme()->hasOption('page', 'assets/vendors/js'))
    {{-- begin::Page Vendors Javascript(used by this page) --}}
    @foreach (array_unique(theme()->getOption('page', 'assets/vendors/js')) as $file)
        <script src="{{ asset(theme()->getDemo() . '/' .$file) }}"></script>
    @endforeach
    {{-- end::Page Vendors Javascript --}}
@endif

@if (theme()->hasOption('page', 'assets/custom/js'))
    {{-- begin::Page Custom Javascript(used by this page) --}}
    @foreach (array_unique(theme()->getOption('page', 'assets/custom/js')) as $file)
        <script src="{{ asset(theme()->getDemo() . '/' .$file) }}"></script>
    @endforeach
    {{-- end::Page Custom Javascript --}}
@endif
{{-- end::Javascript --}}

@if (theme()->getViewMode() === 'preview')
    {{ theme()->getView('partials/trackers/_ga-tag-manager-for-body') }}
@endif
@include('includes.helper-js')
<script src="{{ asset('library/jquery.validate/jquery.validate.min.js') }}"></script>
<script src="{{ asset('library/jquery.form/jquery.form.min.js') }}"></script>
@if (request()->get("vue") == "true")
    <script src="{{ asset('/js/appVue.js') }}"></script>
@endif
<script src="{{ asset('/custom-js/main.min.js') }}"></script>

@include('includes.notification-js')
@include('includes.messaging')
<script src="{{ asset('js/laravelEcho.js') }}"></script>
@if (auth()->check())
    <script src="{{ url('demo1/plugins/custom/datatables/datatables.bundle.js')}}"></script>
    {{-- <script src="{{ asset('demo1/plugins/custom/datatables/datatables.bundle.min.js')}}"></script> --}}
    {{-- <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script> --}}
@endif
@include('includes.ajax-drawer')
@include('includes.ajax-modal')
@include('includes.emailing')
@yield('dynamic_script')
@yield('scripts')
@include('includes.debugs')

@if (auth()->check())
    @include('includes.ticket-notification')
    @include('includes.permanent-notification')
@endif
<style>
    .to-link{
        cursor: pointer;
    }
</style>
</body>
{{-- end::Body --}}
</html>
