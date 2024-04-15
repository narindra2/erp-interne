<div class="card-toolbar my-1">
    <div class="row">
        @foreach ($inputs as $input)
            <div class="me-1 my-2 {{ get_array_value($input, 'width' ,"w-200px")}} ">
                @php
                    $class = "form-control form-control-sm form-control-solid  $filter_for";
                    if (get_array_value($input, 'type') == 'select') {
                        $attributes = get_array_value($input, 'attributes',[]);
                        $input['attributes']['data-hide-search'] = get_array_value($attributes, 'data-hide-search',"true") ;
                        $class = " form-select form-select-solid form-select-sm $filter_for";
                        if (isset($attributes["class"])) {
                            $class  = get_array_value($attributes, 'class') .  " "  . $filter_for;
                        }
                        $input['attributes']['data-control'] = 'select2';
                    }
                    $input['attributes']['class'] = $class ;
                @endphp
                @include("field-inputs.".get_array_value($input,"type") ,["input" => $input])
            </div>
        @endforeach
    </div>
</div>
