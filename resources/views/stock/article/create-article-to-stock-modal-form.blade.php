<form class="form-save-new-inventory" action="{{ url("/stock/inventory/save-new-article") }}" method="POST" id="form-save-new-inventory">
    <div class="card-body">
        @csrf
        <div class="separator border-info mt-3 mb-3"></div>
        <div class="row">
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Code article </span>
                <input type="text" style="cursor: no-drop;"   class="form-control  form-control-sm form-control-solid" readonly value="AUTO-INCREMENTER" />
            </div>
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Matériel à ajouter  </span>
                <select id="item_type_id" name="item_type_id" class="form-select form-select-sm form-select-solid" data-placeholder="Selectionner ..."  data-control="select2" data-hide-search="false" data-dropdown-parent="#ajax-modal">
                    <option value="" selected >Aucun</option>
                    @foreach ($articles as $article)
                        <option value="{{ $article->id }}" >{{ $article->name }}</option>
                    @endforeach      
                </select>
            </div>
            <div class="col-md-4">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Etat du matériel</span>
                    <select id="etat" name="etat" class="form-select form-select-sm form-select-solid" data-placeholder="Etat"  data-control="select2" data-hide-search="true" data-dropdown-parent="#ajax-modal">
                        <option value="fonctionnel" selected >Fonctionnel</option>
                        {{-- <option value="en_stock" >En stock</option> --}}
                        <option value="detruit"   >Détruit</option>
                        <option value="perdu"  >Perdu</option>
                        <option value="en_panne"   >En panne</option>
                    </select>
                </div>
            </div>
           
        </div>
        <div class="separator border-info mt-3 mb-3"></div>
        <div class="row">
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Date d'aquisation </span>
                <input id="date" name="date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off"  placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" value="{{ now()->format("d/m/Y")   }}"/>
            </div>
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Venant du demande d'achat   </span>
                <select id="purchase_id" name="purchase_id" class="form-select form-select-sm form-select-solid" data-placeholder="Selectionner ..."  data-control="select2" data-hide-search="false" data-dropdown-parent="#ajax-modal">
                    <option value="0" selected >Aucun</option>
                    @foreach ($purchases as $purchase)
                        <option value="{{ $purchase->id }}" >N° {{ $purchase->getNumPurchase() . " " .  "(demandé par {$purchase->author->sortname})" }}</option>
                    @endforeach      
                </select>
            </div>
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Lié au numéro facture  </span>
                <select id="num_invoice_id" name="num_invoice_id" class="form-select form-select-sm form-select-solid" data-tags="true" data-placeholder="Saissis + touche entre pour ajouter ..."  data-control="select2" data-allow-clear="true" data-hide-search="false" data-dropdown-parent="#ajax-modal">
                    <option value="0" selected >Aucun</option>
                    @foreach ($num_invoices as $num)
                        <option value="{{ $num->id }}" >{{ $num->num_invoice }}</option>
                    @endforeach      
                </select>
            </div>
        </div>
        <div class="separator border-info mt-3 mb-3"></div>
        <div class="row">
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Prix HT  </span>
                <input type="number" min="10"  class="form-control  form-control-sm form-control-solid" name= "price_ht" value="" placeholder="Prix HT"/>
            </div>
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Prix HTT  </span>
                <input type="number"  min="10"  class="form-control  form-control-sm form-control-solid" name= "price_htt" value=""  placeholder="Prix HTT"/>
                
            </div>
        </div>
        <div class="separator border-info mt-3 mb-3"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Propriété : </span>
                    <textarea id="propriety" name="propriety" class="form-control form-control form-control-sm form-control-solid" rows="3" data-kt-autosize="true"  placeholder="Ex:Marque, couleur , ..." > </textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Observation : </span>
                    <textarea id="observation" name="observation" class="form-control form-control form-control-sm form-control-solid" rows="3" data-kt-autosize="true"  placeholder="Obsevration" > </textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end ">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn  btn-sm btn-secondary">Quitter </button>
        <button type="submit" class="btn btn-sm btn-light-info mx-4">
            @include('partials.general._button-indicator', ['label' =>"Enregistrer maintenant" ,"message" => trans('lang.sending')])
        </button>
    </div>
</form>
<style>
    #modal-dialog{
        min-width: 980px;
    }
    .form-save-new-inventory .form-control.form-control-solid {
        background-color: #F5F8FA;
        border-color: #F5F8FA;
        color: #7239ea !important;
        transition: color 0.2s ease, background-color 0.2s ease;
    }
    .form-save-new-inventory .select2-container--bootstrap5 .select2-selection--single.form-select-solid .select2-selection__rendered {
        color: #7239ea;
    }
</style>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#form-save-new-inventory").appForm({
            onSuccess: function(response) {
                dataTableaddRowIntheTop(dataTableInstance.invetoryListDataTable,response.data) 
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