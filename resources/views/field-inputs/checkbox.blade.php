@php

$field = $input;
$attributes = get_array_value($input, 'attributes');
$html = "";
if ($attributes) {
    foreach ($attributes as $key => $value) {
        $html .= ' ' . $key . '="' . $value . '"';
    }
}
@endphp

<div class="form-check form-check-custom mt-2 form-check-solid">
    <input name="{{ get_array_value($field, 'name') }}" class="{{ get_array_value($attributes, 'class') }}"  id="{{ get_array_value($field, 'name') }}"  {!! $html !!} type="checkbox" value="{{ get_array_value($field, 'value') }}" @if ( get_array_value($field, 'checked')) checked @endif  />
    <label class="form-check-label" for="{{ get_array_value($field, 'name') }}">
        {{get_array_value($field, 'label') }}
    </label>
</div>