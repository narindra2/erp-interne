@php
    $label = $label ?? __('Submit');
    $message = $message ?? __('Please wait...');
    $id =  $id ?? "indicator-label";
@endphp

<!--begin::Indicator-->
<span class="indicator-label" id="{{ $id }}">
    {!! $label !!}
</span>
<span class="indicator-progress">
    {!! $message !!}
    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
</span>
<!--end::Indicator-->
