<form action="{{ url("/days-off/giveResult") }}" method="POST" id="purchases-modal-form">
    <div class="card-body">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Demandeur :  <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  $auth->sortname }}</span></span>  
                </div>
            </div>
       </div>
       <div class="separator border-info mt-3 mb-3"></div>
       <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Ajouter autre demandeur : </span>
                    <div class="d-flex align-items-center">
                        <select name="applicant[]" class="form-select form-select-solid form-control-lg"
                    data-rule-required="true" data-msg-required="@lang('lang.required_input')"
                    data-dropdown-parent="#ajax-modal" data-control="select2" multiple
                    data-placeholder="Ajouter autre demandeur" data-allow-clear="true" data-hide-search= "false">
                    <option disabled  value="0"> -- Demandeur --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"  @if ($user->id  == $auth->id) selected  disabled @endif   >{{ $user->sortname }} </option>
                    @endforeach
                </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Date d'achat :</span>
                    <div class="d-flex align-items-center">
                        <input id="purchase_date" name="purchase_date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid  datepicker" autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" value=""/>
                    </div>
                </div>
            </div>
            
           
       </div>
       <div class="separator border-info mt-3 mb-3"></div>
       <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Paiement par : </span>
                    <div class="d-flex align-items-center">
                        <select class="form-select form-select-solid"
                        name="method" data-hide-search="true" data-control="select2" data-placeholder="Paiement par "  tabindex="-1">
                        <option value="Carte (VISA)">Carte (VISA)</option>
                        <option value="Carte (MASTERCARD)">Carte (MASTERCARD)</option>
                        <option value="Chèque">Chèque</option>
                        <option value="Espèce">Espèce</option>
                    </select>
                    </div>
                </div>
            </div>
       </div>
       <div class="separator border-info mt-3 mb-3"></div>
       
       <div class="col-md-12">
            <div class="card-title d-flex flex-column">   
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Note : </span>
                <textarea id="note"  name="note" class="form-control form-control form-control-solid" rows="1" data-kt-autosize="true" data-rule-required="fales" ></textarea>
            </div>
        </div>
       <div class="separator border-info mt-3 mb-3"></div>

       <div class="row">
            <div class="col-md-12">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6"> Items : </span>
                    <div class="d-flex align-items-center">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-rounded table-row-bordered border gy-4 gs-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                        <th class="min-w-150px">Article   <span  id="addLine"><i class="fs-3 fas fa-plus-circle text-info to-link "></i></span></th>
                                        <th class="min-w-100px">Quantité</th>
                                        <th class="min-w-100px">Unité</th>
                                        <th class="min-w-100px">Prix Unitaire</th>
                                        <th class="min-w-100px">Total</th>
                                        <th class="min-w-100px"></th>
                                    </tr>
                                </thead>
                             
                                <tbody class="item-list">
                                    
                                    <tr class="add_tr d-none">
                                        <!--begin::Product-->
                                        <td class="text-start w-200px">
                                            <select class="form-control form-control-sm "  name="item_type_id[]" data-hide-search="false" data-control="select2" data-placeholder="Materiel ..."  data-dropdown-parent="#ajax-modal">
                                                @foreach ($itemTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <!--end::Product-->
                                        <!--begin::Quantity-->
                                        <td class="text-end">
                                            <input type="number" class="form-control  form-control-sm  w-100px calcul quantity" name="quantity[]" min="1" value="1">
                                        </td>
                                        <!--end::Quantity-->
                                        <!--begin::Quantity-->
                                        <td>
                                            <select class="form-control form-control-sm "  name="unit_item_id[]" data-hide-search="true" data-control="select2" data-placeholder="Paiement par ">
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <!--end::Quantity-->
                                        <!--begin::Price-->
                                        <td class="text-end">
                                            <input type="number"
                                                class="form-control   form-control-sm w-100px calcul unitPrice"
                                                name="unit_price[]" min="0" value="0">
                                        </td>
                                        <!--end::Price-->
                                        <!--begin::Total-->
                                        <td class="mt-2"><input type="text" class="form-control form-control-sm form-control-transparent total" value="0"/></td>
                                        <td class="text-center">
                                            <span class="to-link " title="Supprimer cette ligne" onclick="deleteLine(this)"><i  class="far fa-trash-alt text-danger  "></i></span>
                                        </td>
                                        <!--end::Total-->
                                    </tr>
                                    <tr class="add_tr">
                                        <!--begin::Product-->
                                        <td class="text-start w-200px">
                                            <select class="form-control form-control-sm "  name="item_type_id[]" data-hide-search="false" data-control="select2" data-placeholder="Materiel ..."  data-dropdown-parent="#ajax-modal">
                                                <option disabled value="0" selected> Liste des arcticles..</option>
                                                @foreach ($itemTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <!--end::Product-->
                                        <!--begin::Quantity-->
                                        <td class="text-end">
                                            <input type="number" class="form-control  form-control-sm  w-100px calcul quantity" name="quantity[]" min="1" value="1">
                                        </td>
                                        <!--end::Quantity-->
                                        <!--begin::Quantity-->
                                        <td>
                                            <select class="form-control form-control-sm "  name="unit_item_id[]" data-hide-search="true" data-control="select2" data-placeholder="Paiement par ">
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <!--end::Quantity-->
                                        <!--begin::Price-->
                                        <td class="text-end">
                                            <input type="number"
                                                class="form-control   form-control-sm w-100px calcul unitPrice"
                                                name="unit_price[]" min="0" value="0">
                                        </td>
                                        <!--end::Price-->
                                        <!--begin::Total-->
                                        <td class="mt-2"><input type="text" class="form-control  form-control-sm form-control-transparent total" value="0"/></td>
                                        <td class="text-center">
                                            <span class="to-link " title="Supprimer cette ligne" onclick="deleteLine(this)"><i  class="far fa-trash-alt text-danger  "></i></span>
                                        </td>
                                        <!--end::Total-->
                                    </tr>
                                    <tr>
                                        
                                        <td colspan="4" class="fs-3 text-dark">Prix Total</td>
                                        {{-- <td class="text-dark fs-3 fw-boldest" id="totalPrice">0</td> --}}
                                        <td class="text-dark fs-3 fw-boldest" id="totalPrice">0</td>
                                    </tr>
                                    <!--end::Grand total-->
                                </tbody>
                                <!--end::Table head-->
                            </table>
                            <!--end::Table-->
                            <div class="d-flex justify-content-end mt-5">
                                <button class="btn btn-light-success font-weight-bold mr-2 btn-sm">Sauvegarder</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       </div>
    </div>
</form>
<script>
    var minItem = 1;
    var deleteLine;
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#purchases-modal-form").appForm({
            onSuccess: function(response) {
                if (dataTableInstance.my_days_off) {
                    dataTableInstance.my_days_off.ajax.reload();
                }
                if (dataTableInstance.dayOffRequested) {
                    dataTableInstance.dayOffRequested.ajax.reload();
                    // reload gantt 
                    loadGantt();
                }
            },
        });
        $("#addLine").on("click", function() {
            minItem++;
            $("select.select2-hidden-accessible").select2('destroy');
            $(".add_tr").eq(0).clone().insertAfter(".add_tr:last").removeClass("d-none");
            KTApp.initSelect2();
        });
        deleteLine = function deleteLine(content) {
            if (minItem > 1) {
                minItem--;
                $(content).closest(".add_tr").remove();
            }
            caclucTotal();
        }
        function caclucTotal (){
            let totalPrice = 0;
            $(".add_tr").each((i) => {
                let quantity = $(".add_tr").eq(i).find(".quantity").val();
                let unitPrice = $(".add_tr").eq(i).find(".unitPrice").val();
                let totalOneLine = parseFloat(quantity) * parseFloat(unitPrice);
                $(".add_tr").eq(i).find(".total").val(totalOneLine);
                totalPrice += totalOneLine;
            });
            $("#totalPrice").text(totalPrice);
        }
        
        $(document).on("keyup change", ".calcul", () => {
            caclucTotal();
        });
       
        function init_date(){
                var format = 'DD/MM/YYYY';
                var monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                var daysOfWeek =['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven','Sam'];
                var deliver = $(".datepicker").daterangepicker({
                    singleDatePicker: true,
                    drops: 'auto',
                    autoUpdateInput: false,
                    autoApply: true,
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
        init_date(); 
    });
</script>