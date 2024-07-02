<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"{!! theme()->printHtmlAttributes('html') !!} {{ theme()->printHtmlClasses('html') }}>
{{-- begin::Head --}}
<head>
    <meta charset="utf-8"/>
    <title>ERP | Desineo </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
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
</head>
<body {!! theme()->printHtmlAttributes('body') !!} {!! theme()->printHtmlClasses('body') !!} {!! theme()->printCssVariables('body') !!}>
<div class=" app-content  flex-column-fluid container mt-10">
    <div class="col-xl-4 mb-xl-10">
        <div class="card card-flush h-xl-100  shadow-sm ">
            <div class="card-header pt-7 ">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-info">{{ $item->article->name }}</span>
                    <span class="text-gray-500  fw-semibold fs-6">
                        Propieté : {{ $item->propriety ?? "-" }}<br>
                        
                        @if ($item->article->category)
                            {{ $item->article->category->name }}
                        @endif
                        @if ($item->article->sub_category)
                          ,  {{ $item->article->sub_category  }}
                        @endif
                    </span> <br>
                </h3>
            </div>
                <div class="card-body bg-white">
                    <div class="tab-content" >
                        <div class="tab-pane fade show active" id="detai" role="tabpanel">
                            <div class="mb-4 text-center">
                                {{ $item->qrCode }}
                            </div>
                            <div class="mb-4 text-center text-info"><strong>{{ $item->codeDetail }}</strong></div>
                            <div class="mb-4">
                                <label class="form-label">Date d'aquisition</label>
                                <input type="text" class="form-control form-control-sm form-control-solid" readonly  value="{{ $item->date->format("d/m/Y") }}">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Etat</label>
                                <input type="text" class="form-control form-control-sm form-control-solid" readonly  value="{{ $item->etat }}">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Prix HT</label>
                                <input type="text" class="form-control form-control-sm form-control-solid" readonly  value="{{ $item->price_ht ?? "-" }}">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Observation </label>
                                <input type="text" class="form-control form-control-sm form-control-solid" readonly  value="{{ $item->observation ?? "Aucun" }}">
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>




<style>
    .tab-content .form-control.form-control-solid {
        background-color: #F5F8FA;
        border-color: #F5F8FA;
        color: #7239ea !important;
        transition: color 0.2s ease, background-color 0.2s ease;
    }
    .to-link{
        cursor: pointer;
    }
</style>
</body>

</html>
