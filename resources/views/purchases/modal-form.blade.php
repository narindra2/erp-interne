<form action="{{ url('/purchases/save') }}" method="POST" id="purchases-modal-form">
    <div class="card-body">
        @csrf
        @if ($purchase_model->id)
            <input type="hidden" name="is_update" value="true">
            <input type="hidden" name="purchase_id" value="{{ $purchase_model->id }}">
        @endif
        <div class="separator border-info mt-3 mb-3"></div>
        <div class="row">
            <div class="col-md-8">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Demandeur :  <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{ $purchase_model->author->sortname ?? $auth->sortname }}</span></span>  
                </div>
            </div>
            @if ($purchase_model->id)
                <div class="col-md-4">
                    <div class="card-title d-flex flex-column">   
                        <span class="text-gray-700 pt-1 fw-semibold fs-6">Demandé :  <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{ convert_to_real_time_humains($purchase_model->created_at)}}</span></span>  
                    </div>
                </div>
            @endif
       </div>
       <div class="separator border-info mt-3 mb-3"></div>
       <div class="row">
            
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Paiement par : </span>
                    <div class="d-flex align-items-center">
                        <select class="form-select form-select-sm form-select-solid"
                        name="method" data-hide-search="true" data-control="select2" data-placeholder="Paiement par "  tabindex="-1">
                        <option @if ($purchase_model->method == "Carte (VISA)") selected  @endif value="Carte (VISA)">Carte (VISA)</option>
                        <option @if ($purchase_model->method == "Carte (MASTERCARD)") selected  @endif value="Carte (MASTERCARD)">Carte (MASTERCARD)</option>
                        <option @if ($purchase_model->method == "Chèque") selected  @endif value="Chèque">Chèque</option>
                        <option @if ($purchase_model->method == "Espèce") selected  @endif value="Espèce">Espèce</option>
                    </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6 form-group">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Date d'achat :</span>
                    <div class="d-flex align-items-center ">
                        <input id="purchase_date" name="purchase_date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid  datepicker"
                        autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" value="{{ $purchase_model->id ? $purchase_model->purchase_date->format("d/m/Y") : ''}}"/>
                       
                    </div>
                </div>
            </div>
       </div>

       <div class="separator border-info mt-3 mb-3"></div>
       <div class="row">
        <div class="col-md-12">
            <div class="card-title d-flex flex-column">   
                <span class="text-gray-700 pt-1 fw-semibold fs-6">{{ $purchase_model->id ?  "Les personnes" :  "Taguer  les personnes"  }}  uniques qui seront informées  de cette demande  : </span>
                <div class="d-flex align-items-center form-group">
                    @include('tasks.kanban.users-tag', [
                            'users' => $users,
                            'default' => $defaut_tagged,
                            'placeholder' => 'Personnes  ...',
                        ])
                </div>
            </div>
        </div>
       </div>
       <div class="separator border-info mt-3 mb-3"></div>
       
       <div class="col-md-12">
            <div class="card-title d-flex flex-column">   
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Note : </span>
                <textarea id="note"  name="note" placeholder="Note concernant de la demande d 'achat ..." class="form-control form-control form-control-solid" rows="1" data-kt-autosize="true" data-rule-required="fales" >{{ $purchase_model->note }}</textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="row">
                @if ($purchase_model->id)
                    <div class="col-md-6">
                        <div class="card-title d-flex flex-column">   
                            <span class="text-gray-700 pt-1 fw-semibold fs-6  mb-1">Fichiers joints :</span>
                            @if ($purchase_model->files->count())
                                <div class="row">
                                @include('purchases.columns.files', ['files' => $purchase_model->files])
                                </div>
                            @else 
                                <i class="mt-2" >Pas de piéce joint ajouté.</i>
                            @endif
                        </div>
                    </div>   
                @endif
               
                <div class="col-md-{{  $purchase_model->id ? "6" : "12" }}">
                    <div class="card-title d-flex flex-column">   
                        <span class="text-gray-700 pt-1 fw-semibold fs-6 mb-1">Ajouter {{ $purchase_model->id ? "autre" : "" }} des fichiers joints : </span>
                        <input class="form-control form-control-sm" name="files[]" type="file" multiple>
                    </div>
                </div>
            </div>
        </div>
       <div class="separator border-info mt-3 mb-3"></div>

       <div class="row">
            <div class="col-md-12">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6"> Articles à acheté : </span>
                    <div class="d-flex align-items-center">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-rounded table-row-bordered border gy-4 gs-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                        <th class="min-w-150px text-center"> <span title="Ajouter une ligne"  id="addLine"><i class="fs-3 fas fa-plus-circle text-info to-link "></i></span></th>
                                        <th class="min-w-150px text-center">Propriété</th>
                                        <th >Quantité </th>
                                        <th >Unité</th>
                                        <th >Prix unitaire</th>
                                        <th >Total</th>
                                        <th class=""></th>
                                    </tr>
                                </thead>
                             
                                <tbody class="item-list">
                                    
                                    <tr class="add_tr d-none">
                                        <!--begin::Product-->
                                        <td class="text-start w-200px">
                                            <select class="form-control form-control-sm "  name="item_type_id[]" data-hide-search="false" data-control="select2" data-placeholder="Materiel ..."  data-dropdown-parent="#ajax-modal">
                                                <option  value="0" selected> Liste des arcticles..</option>
                                                @foreach ($itemTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <!--end::Product-->
                                        <!--begin::Quantity-->
                                        <td class="text-end w-200px">
                                            <input type="text " autocomplete="off" class="form-control  form-control-sm  w-200px " placeholder="Son marque ,taille , poids , ..." name="proprieties[]"  value="">
                                        </td>
                                        <td class="text-end ">
                                            <input type="number"  autocomplete="off" class="form-control  form-control-sm  w-75px calcul quantity" name="quantity[]" min="1" value="1">
                                        </td>
                                       
                                        <!--end::Quantity-->
                                        <!--begin::Quantity-->
                                        <td>
                                            <select class="form-control form-control-sm   w-75px "  name="unit_item_id[]" data-hide-search="true" data-control="select2" data-placeholder="Paiement par ">
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <!--end::Quantity-->
                                        <!--begin::Price-->
                                        <td class="text-end">
                                            <input type="number" autocomplete="off" class="form-control   form-control-sm w-150px calcul unitPrice"  name="unit_price[]" min="0" value="0">
                                        </td>
                                        <!--end::Price-->
                                        <!--begin::Total-->
                                        <td class="mt-2"><input type="text"  autocomplete="off" class="form-control form-control-sm form-control-transparent total" value="0"/></td>
                                        <td class="text-center">
                                            <span class="to-link " title="Supprimer cette ligne" onclick="deleteLine(this)"><i  class="far fa-trash-alt mt-3 text-danger  "></i></span>
                                        </td>
                                        <!--end::Total-->
                                    </tr>

                                    @forelse ($purchase_model->details as $item )
                                        <tr class="add_tr">
                                            <!--begin::Product-->
                                            <td class="text-start w-200px">
                                                <select class="form-control form-control-sm "  name="item_type_id[]" data-hide-search="false" data-control="select2" data-placeholder="Materiel ..."  data-dropdown-parent="#ajax-modal">
                                                    <option  value="0" > Liste des arcticles..</option>
                                                    @foreach ($itemTypes as $type)
                                                        <option value="{{ $type->id  }}" @if ($item->item_type_id == $type->id)  selected @endif >{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <!--end::Product-->
                                            <td class="text-end w-200px">
                                                <input type="text " class="form-control  form-control-sm w-200px "placeholder="Ex:Son marque ,taille , poids" name="proprieties[]"  value="">
                                            </td>
                                            <!--begin::Quantity-->
                                            <td class="text-end">
                                                <input type="number" class="form-control  form-control-sm  w-75px calcul quantity" name="quantity[]" min="1" value="{{ $item->quantity }}">
                                            </td>
                                           
                                            <!--end::Quantity-->
                                            <!--begin::Quantity-->
                                            <td>
                                                <select class="form-control form-control-sm  w-75px"  name="unit_item_id[]" data-hide-search="true" data-control="select2" >
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}" @if ($item->unit_item_id == $unit->id)  selected @endif>{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <!--end::Quantity-->
                                            <!--begin::Price-->
                                            <td class="text-end">
                                                <input type="number" class="form-control   form-control-sm w-150px calcul unitPrice" name="unit_price[]" min="0" value="{{ $item->unit_price }}">
                                            </td>
                                            <!--end::Price-->
                                            <!--begin::Total-->
                                            <td class="mt-2"><input type="text" autocomplete="off" class="form-control  form-control-sm form-control-transparent total" value="{{ $item->unit_price * $item->quantity }}"/></td>
                                            <td class="text-center">
                                                <span class="to-link " title="Supprimer cette ligne" onclick="deleteLine(this)"><i  class="far fa-trash-alt mt-3 text-danger  "></i></span>
                                            </td>
                                            <!--end::Total-->
                                        </tr>
                                    @empty
                                        {{-- <tr class="add_tr">
                                           
                                        </tr> --}}
                                    @endforelse
                                    <tr>
                                        
                                        <td colspan="4" class="fs-4 text-dark">Prix Total</td>
                                        {{-- <td class="text-dark fs-3 fw-boldest" id="totalPrice">0</td> --}}
                                        <td class="text-info fs-3 fw-boldest" id="totalPrice">{{ $purchase_model->id ? $purchase_model->total_price : 0   }}</td>
                                    </tr>
                                    <!--end::Grand total-->
                                </tbody>
                                <!--end::Table head-->
                            </table>
                            <!--end::Table-->
                           
                        </div>
                    </div>
                </div>
            </div>
       </div>

       <span class="text-gray-700 pt-1 fw-semibold fs-6 mb-3">Status  : </span>
       <div data-kt-buttons="true">
        <div class="row">
            <div class="col-md-3">
                <label class="btn btn-sm btn-outline  btn-active-light-primary d-flex flex-stack text-start">
                    <div class="d-flex align-items-center me-2">
                        <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                            <input class="form-check-input" @if ($purchase_model->status == "in_progress" || !$purchase_model->id) checked  @endif type="radio" name="status" value="in_progress"/>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="d-flex align-items-center fs-4 fw-bold flex-wrap">
                                En attente 
                            </h4>
                        </div>
                    </div>
                </label>
            </div>
            <div class="col-md-3 "  @if (!$purchase_model->id)   style ="opacity: 0.3; "  title= "Non utlisable pour une nouvelle demande" @endif >
                <label class="btn btn-sm btn-outline  btn-active-light-danger d-flex flex-stack text-start">
                    <div class="d-flex align-items-center me-2">
                        <div class="form-check form-check-custom form-check-solid form-check-danger me-6">
                            <input   @if (!$purchase_model->id) disabled  style ="opacity: 0.3;"  @endif class="form-check-input" @if ($purchase_model->status == "refused") checked @endif type="radio" name="status" value="refused"/>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="d-flex align-items-center fs-4 fw-bold flex-wrap">
                                Refusé
                            </h4>
                        </div>
                    </div>
                </label>
            </div>
            <div class="col-md-3">
                <label class="btn btn-sm btn-outline  btn-active-light-success d-flex flex-stack text-start">
                    <!--end::Description-->
                    <div class="d-flex align-items-center me-2">
                        <!--begin::Radio-->
                        <div class="form-check form-check-custom form-check-solid form-check-success me-6">
                            <input class="form-check-input" @if ($purchase_model->status == "validated") checked  @endif type="radio" name="status" value="validated"/>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="d-flex align-items-center fs-4 fw-bold flex-wrap">
                                Validé
                            </h4>
                        </div>
                    </div>
                </label>
            </div>
            
            <div class="col-md-3">
                <label class="btn btn-sm btn-outline  btn-active-light-info d-flex flex-stack text-start">
                    <!--end::Description-->
                    <div class="d-flex align-items-center me-2">
                        <!--begin::Radio-->
                        <div class="form-check form-check-custom form-check-solid form-check-info me-6">
                            <input class="form-check-input" @if ($purchase_model->status == "purchased") checked  @endif type="radio" name="status" value="purchased"/>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="d-flex align-items-center fs-4 fw-bold flex-wrap">
                                Achat fait
                            </h4>
                        </div>
                    </div>
                </label>
            </div>
          
        </div>
        </div>
        </div>
     @php
        $isRhOrAdmin = $auth->isRhOrAdmin();
     @endphp
    <div class="d-flex justify-content-end mt-5">
        @if ($purchase_model->id)
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-dark btn-sm mr-2 ">
                Quitter          
            </button> &nbsp;
            @if ($isRhOrAdmin)
                <button type="submit" class="btn btn-dark font-weight-bold mr-2 btn-sm">Sauvegarder</button>
            @endif
        @else 
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-dark btn-sm mr-2 ">
                Annuler 
            </button> &nbsp;
            @if ($isRhOrAdmin)
                <button type="submit" class="btn btn-light-info font-weight-bold mr-2 btn-sm">Créer la demande</button>
            @endif

        @endif
    </div>
</form>
<style>
    #modal-dialog{
        min-width: 980px;
    }
</style>
<script>
    @if ($purchase_model->id && $purchase_model->details->count() )
        var minItem = {{ $purchase_model->details->count() }};
    @else 
        var minItem = 0;
    @endif      
   
    var deleteLine;
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#purchases-modal-form").appForm({
            onSuccess: function(response) {
                dataTableInstance.purchasesTable.ajax.reload();
            },
        });
        $("#addLine").on("click", function() {
            minItem++;
            $("select.select2-hidden-accessible").select2('destroy');
            $(".add_tr").eq(0).clone().insertAfter(".add_tr:last").removeClass("d-none");
            KTApp.initSelect2();
        });
        deleteLine = function deleteLine(content) {
            if (minItem >= 0) {
                minItem--;
                $(content).closest(".add_tr").remove();
            }
            caclucTotal();
        }
        function convertTo2Decimal(decimal = 0){
            if (decimal =="NaN") {
                return 0;
            }
            return Math.round((decimal + Number.EPSILON) * 100) / 100
        }
        function caclucTotal (){
            let totalPrice = 0;
            $(".add_tr").each((i) => {
                let quantity = $(".add_tr").eq(i).find(".quantity").val();
                let unitPrice = $(".add_tr").eq(i).find(".unitPrice").val();
                let totalOneLine = parseFloat(quantity) * parseFloat(unitPrice);
                $(".add_tr").eq(i).find(".total").val( convertTo2Decimal(totalOneLine) );
                totalPrice += totalOneLine;
            });
            $("#totalPrice").text(convertTo2Decimal(totalPrice));
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