@php
$field = $input;
$options = get_array_value($input, 'options');
$attributes = get_array_value($input, 'attributes');
$attr = '';

if ($attributes) {
    foreach ($attributes as $key => $value) {
        $attr .= ' ' . $key . '="' . $value . '"';
    }
}
@endphp
<div class="row">
<input {!! $attr !!} name="{{ get_array_value($input, 'name') }}" value="" autocomplete="off"  id="{{ get_array_value($input, 'name') }}" />
</div>
<script>
    $(document).ready(function() {
         $("#{{ get_array_value($input, 'name') }}").daterangepicker({
            autoApply: false,
            autoUpdateInput: false,
            stickyMonths: true,
            linkedCalendars: true,
            locale: {
                defaultValue: "",
                format: 'DD/MM/yyyy',
                applyLabel: "{{ trans('lang.apply') }}",
                cancelLabel: "{{ trans('lang.cancel') }}",
            },
        }).val('').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/yyyy') + "-" + picker.endDate.format('DD/MM/yyyy'))
            $(this).change()
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val("")
            $(this).change()
        });
    })
</script>