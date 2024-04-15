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

<input type="text" name="{{ get_array_value($field, 'name') }}" id="{{ get_array_value($field, 'name') }}" autocomplete="off" placeholder="{{ get_array_value($field, 'label') }}" {!! $html !!}>