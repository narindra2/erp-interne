<x-base-layout>
    @include('cerfa.includes.header')
    <div class="card card-flush shadow-sm">
        @if ($customer->id!=null && $projects->id!=null)
            <form action="{{ url("/cerfa/project/store/$customer->id/$projects->id") }}" id="cerfa_form" method="POST">
        @else
            <form action="{{ url("/cerfa/project/store") }}" id="cerfa_form" method="POST">
        @endif
            @csrf
            @if ($customer->id!=null)
                <input type="hidden" value="{{ $customer->id }}" name="customer_id">
                <input type="hidden" value="{{ $projects->id }}" name="project_id">
            @endif
            <div class="card-body">
                <h5 class="card-title text-gray-800">Vous souhaitez déposer le dossier en mairie en tant que :</h5>
                <!--begin::Radio group-->
                <div data-kt-buttons="true">
                    <div class="row mt-10">
                        @foreach ($customer_types as $key => $value)
                            <div class="col-md-6 mb-2 d-flex justify-content-center">
                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary w-300px d-flex flex-stack text-start
                                    {{ $customer->id==null && $value['value']== 2 ? "active" : "" }} ">
                                    <div class="d-flex align-items-center me-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                            <input class="form-check-input" id="customer_type" type="radio" name="customer_type"
                                            @if(($customer->id==null && $value['value']== 2) || ($customer->id!=null && $customer->customer_type_id== $value['value']) )
                                                checked="checked"
                                            @endif
                                            value="{{ $value['value'] }}"/>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                                {{ $value['text'] }}
                                            </h2>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!--end::Radio group-->
                <div class="row mt-20 society">
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Quelle est la dénomination sociale de la société ?
                        </label>
                        <input type="text" name="denomination" @if($customer->id!=null) value = "{{ $customer->denomination }}" @endif
                         data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Quelle est la raison sociale de la société ?
                        </label>
                        <input type="text" name="social_reason" @if($customer->id!=null) value = "{{ $customer->social_reason }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Quel est le type de société ?
                        </label>
                        <input type="text" name="society_type" @if($customer->id!=null) value = "{{ $customer->society_type }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Quel est le numéro de SIRET de la société ?
                        </label>
                        <input type="text" name="siret_number" @if($customer->id!=null) value = "{{ $customer->siret_number }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Quelle est la civilité du déclarant ?
                        </label>
                        <div class="mb-10 d-flex mx-20">
                            @for($i=0; $i<count($civilities);$i++)
                                <div class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-15px w-15px" type="radio"
                                    @if(($customer->id==null && $i==0) || ($customer->id!=null && $customer->civility== $i)) checked="checked" @endif
                                    name="civility" value="{{ $i }}" id="flexCheckbox{{ $i }}">
                                    <label class="form-check-label" for="flexCheckbox{{ $i }}">{{ $civilities[$i] }}</label>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Quel est son nom ?
                        </label>
                        <input type="text" name="lastname" @if($customer->id!=null) value = "{{ $customer->lastname }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="form-label">
                            Quel est son prénom ?
                        </label>
                        <input type="text" name="firstname" @if($customer->id!=null) value = "{{ $customer->firstname }}" @endif class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Quelle est sa date de naissance ?
                        </label>
                        <input name="birthday" @if($customer->id!=null && !empty($customer->birthday))
                            value="{{ convert_database_date($customer->birthday) }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" autocomplete="off" id="birthday" class="form-control form-control-solid birthday"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Quel est son lieu de naissance (*ville et code postal*) ?
                        </label>
                        <input type="text" name="birthday_place" @if($customer->id!=null) value = "{{ $customer->birthday_place }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Quel est son numéro de téléphone ?
                        </label>
                        <input type="text" name="phone_number" @if($customer->id!=null) value = "{{ $customer->phone_number }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" data-rule-required="true" data-msg-required="@lang('lang.required')" class="required form-label">
                            Quel est son adresse e-mail ?
                        </label>
                        <input type="email" name="email" @if($customer->id!=null) value = "{{ $customer->email }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                </div>
                <div class="row">
                    <div class="separator separator-dashed my-8"></div>
                    <h3 class="card-title">Adresse du projet</h3>
                    <div class="col-md-6 mt-5 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Le numéro de la voie
                        </label>
                        <input type="text" name="way_number" @if($customer->id!=null) value = "{{ $projects->way_number }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mt-5 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Le nom de la voie ou du lieu-dit
                        </label>
                        <input type="text" name="locality" @if($customer->id!=null) value = "{{ $projects->locality }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Le code postal
                        </label>
                        <input type="text" name="postal_code" @if($customer->id!=null) value = "{{ $projects->postal_code }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Le nom de la commune
                        </label>
                        <input type="text" name="town" @if($customer->id!=null) value = "{{ $projects->town }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            L'adresse du déclarant est-elle la même que celle du projet ?
                        </label>
                        <div class="mb-10 d-flex mx-20">
                            @for($i=0; $i<count($address_option); $i++)
                                <div class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-15px w-15px"
                                    @if(($customer->id==null && $i==0) || ($customer->id!=null && $customer->option_address== $i)) checked="checked" @endif
                                     type="radio" name="address" value="{{ $i }}" id="flexCheckbox_address{{ $i }}">
                                    <label class="form-check-label" for="flexCheckbox_address{{ $i }}">{{ $address_option[$i] }}</label>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="row declaring_address">
                    <h3 class="card-title">Adresse du déclarant</h3>
                    <div class="col-md-6 mt-5 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Le numéro de la voie
                        </label>
                        <input type="text" name="c_way_number" @if($customer->id!=null) value = "{{ $customer->c_way_number }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6  mt-5 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Le nom de la voie ou du lieu-dit
                        </label>
                        <input type="text" name="c_locality" @if($customer->id!=null) value = "{{ $customer->c_locality }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Le code postal
                        </label>
                        <input type="text" name="c_postal_code" @if($customer->id!=null) value = "{{ $customer->c_postal_code }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6 mb-8">
                        <label for="exampleFormControlInput1" class="required form-label">
                            Le nom de la commune
                        </label>
                        <input type="text" name="c_town" @if($customer->id!=null) value = "{{ $customer->c_town }}" @endif
                        data-rule-required="true" data-msg-required="@lang('lang.required')" class="form-control form-control-solid"/>
                    </div>
                </div>
                <div class="separator separator-dashed my-8"></div>
                <div class="row">
                    <div class="col-5">
                    </div>
                    <div class="col-7">
                        <button type="submit" id="submit" class="btn btn-primary mr-2">@lang('lang.save')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @section('scripts')
        <script>
            $(document).ready(function() {
                init_date();

                $("#cerfa_form").appForm({
                    isModal:false,
                    onSuccess:function(response){
                        window.location.replace("{{ url("/cerfa/customer/") }}");
                    }
                });

                @if ($customer->id!=null && $customer->customer_type_id==1)
                    $(".society").css("display", "none")
                @endif

                @if($customer->id!=null && $customer->option_address== 1)
                    $(".declaring_address").css("display", "");
                @else
                    $(".declaring_address").css("display", "none");
                @endif

                $("input[type='radio']").click(function(){
                    var customer_type_value = $("input[name='customer_type']:checked").val();
                    if(customer_type_value){
                        customer_type_value == '2' ? $(".society").css("display", "") : $(".society").css("display", "none");
                    }
                    var address_value = $("input[name='address']:checked").val();
                    if(address_value){
                        address_value == '1' ? $(".declaring_address").css("display", "") : $(".declaring_address").css("display", "none");
                    }
                });
            })

            function init_date(){
                var format = 'DD/MM/YYYY';
                var monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                var daysOfWeek =['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven','Sam'];
                var deliver = $(".birthday").daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    drops: 'auto',
                    autoUpdateInput: false,
                    autoApply: false,
                    maxDate: new Date(),
                    locale: {
                        defaultValue: "",
                        format: format,
                        applyLabel: "{{ trans('lang.apply') }}",
                        cancelLabel: "{{ trans('lang.cancel') }}",
                        daysOfWeek: daysOfWeek,
                        monthNames: monthNames,
                    },
                });
                deliver.on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(format))
                });
            }
        </script>
    @endsection

</x-base-layout>
