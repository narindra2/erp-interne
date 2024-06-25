<form class="form-inventor" action="{{ url("/stock/inventory/save/inventor/from-edit") }}" method="POST" id="modal-form-inventor">
    <div class="card-body">
        @csrf
        <input type="hidden" name="item_id" value="{{ $item->id }}" >
        <div class="separator border-info mt-3 mb-3"></div>
       <div class="row">
            <div class="col-md-4">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Code article </span>
                    <div class="d-flex align-items-center">
                        <span  style="cursor: no-drop;" class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  $item->code_detail }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Article</span>
                    <div class="d-flex align-items-center">
                        <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{ $item->article->name }}</span>
                    </div>
                </div>
            </div>
            @if ($item->article->sub_category ||  $item->article->category)
                <div class="col-md-4">
                    <div class="card-title d-flex flex-column">   
                        <span class="text-gray-700 pt-1 fw-semibold fs-6">{{ $item->article->category ? $item->article->category->name : "Sous-catégorie" }} </span>
                        <div class="d-flex align-items-center">
                            <span class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">
                                {{ $item->article->sub_category }} 
                            </span>
                        </div>
                    </div>
                </div>  
            @endif
           
       </div>
      
        <div class="separator border-info mt-3 mb-3"></div>
        <div class="row">
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Date d'aquisation </span>
                <input id="date" name="date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off"  placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" value="{{ $item->date ? \Carbon\Carbon::parse($item->date)->format("d/m/Y")   : null }}"/>
            </div>
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Lié au demande d'achat de  </span>
                <select id="purchase_id" name="purchase_id" class="form-select form-select-sm form-select-solid" data-placeholder="Selectionner ..."  data-control="select2" data-hide-search="false" data-dropdown-parent="#ajax-modal">
                    <option value="0" selected >Aucun</option>
                    @foreach ($purchases as $purchase)
                        <option value="{{ $purchase->id }}" @if ($purchase->id  == $item->purchase_id) selected @endif>{{ $purchase->getNumPurchase() . " " .  "({$purchase->author->sortname})" }}</option>
                    @endforeach      
                </select>
            </div>
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Lié au numéro facture  </span>
                <select id="num_invoice_id" name="num_invoice_id" class="form-select form-select-sm form-select-solid" data-tags="true" data-placeholder="Saissis + touche entre pour ajouter ..."  data-control="select2" data-allow-clear="true" data-hide-search="false" data-dropdown-parent="#ajax-modal">
                    <option value="0" selected >Aucun</option>
                    @foreach ($num_invoices as $num)
                        <option value="{{ $num->id }}" @if ($num->id  == $item->num_invoice_id) selected @endif>{{ $num->num_invoice }}</option>
                    @endforeach  
                    {{-- The num invoice save manuely --}}
                    @if (!$item->num_invoices &&  $item->num_invoice_id)
                        <option value="{{ $item->num_invoice_id }}" selected >{{ $item->num_invoice_id }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="separator border-info mt-3 mb-3"></div>
        <div class="row">
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Prix HT  </span>
                <input type="number" min="10"   class="form-control  form-control-sm form-control-solid" name= "price_ht" value="{{ $item->price_ht ?? null }}" placeholder="Prix HT"/>
            </div>
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Prix HTT  </span>
                <input type="number"  min="10"  class="form-control  form-control-sm form-control-solid" name= "price_htt" value="{{ $item->price_htt ?? null }}" placeholder="Prix HTT"/>
                
            </div>
            <div class="col-md-4">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Etat du matériel</span>
                    <select id="etat" name="etat" class="form-select form-select-sm form-select-solid" data-placeholder="Etat"  data-control="select2" data-hide-search="true" data-dropdown-parent="#ajax-modal">
                        <option value="fonctionnel" @if($item->etat == 'fonctionnel') selected @endif>Fonctionnel</option>
                        <option value="detruit" @if($item->etat == 'detruit') selected @endif >Détruit</option>
                        <option value="perdu"  @if($item->etat == 'perdu') selected @endif>Perdu</option>
                        <option value="en_panne" @if($item->etat == 'en_panne') selected @endif  >En panne</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="separator border-info mt-3 mb-3"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Propriété : </span>
                    <textarea id="propriety" name="propriety" class="form-control form-control form-control-sm form-control-solid" rows="2" data-kt-autosize="true"  placeholder="Ex:Marque, couleur , ..." > {{ $item->propriety }} </textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Observation : </span>
                    <textarea id="observation" name="observation" class="form-control form-control form-control-sm form-control-solid" rows="2" data-kt-autosize="true"  placeholder="Obsevration" > {{ $item->observation }} </textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end ">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn  btn-sm btn-secondary">Quitter </button>
        <button type="submit" class="btn btn-sm btn-light-info mx-4">
            @include('partials.general._button-indicator', ['label' =>"Enregistrer la modification" ,"message" => trans('lang.sending')])
        </button>
    </div>
</form>
<style>
    #modal-dialog{
        min-width: 980px;
    }
    .form-inventor .form-control.form-control-solid {
        background-color: #F5F8FA;
        border-color: #F5F8FA;
        color: #7239ea !important;
        transition: color 0.2s ease, background-color 0.2s ease;
    }
    .form-inventor .select2-container--bootstrap5 .select2-selection--single.form-select-solid .select2-selection__rendered {
        color: #7239ea;
    }
</style>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#modal-form-inventor").appForm({
            onSuccess: function(response) {
                if (response.row_id) {
                    dataTableUpdateRow(dataTableInstance.invetoryListDataTable, response.row_id,response.data) 
                }
            },
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