<form class="form-inventor" action="{{ url("/stock/inventory/save/inventor/from-edit") }}" method="POST" id="modal-form-inventor">
    <div class="card-body">
        @csrf
        <input type="hidden" name="item_id" value="{{ $item->id }}" >
        
        <div class="separator border-info mb-2"></div>
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
       <div class="separator border-info mt-2 mb-2"></div>
       <div class="row">
           <div class="col-md-5">
               <div class="card-title d-flex flex-column">   
                   <span class="text-gray-700 pt-1 fw-semibold fs-6">QRcode contenue :</span>
                   <div class="d-flex align-items-center">
                       <span  style="cursor: no-drop;" class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  url("/item/$item->id") }}</span>
                   </div>
               </div>
           </div>
           <div class="col-md-3 ">
               <div class="card-title d-flex flex-column">   
                   <div class="d-flex align-items-center">
                       {{ $item->qrcode }}
                   </div>
               </div>
           </div>
           <div class="col-md-4">
               <div class="card-title d-flex flex-column">   
                   <span class="text-gray-700 pt-1 fw-semibold fs-6">Disponibilité</span>
                   <div class="d-flex align-items-center">
                       <span  style="cursor: no-drop;" class="fs-5 fw-bold text-info me-2 lh-1 ls-n2">{{  $item->get_disponible() }} {{ $item->get_disponible() == "En usage"  ? " de " .$item->get_user_use_it() : "" }}</span>
                   </div>
               </div> 
           </div>
       </div>
        <div class="separator border-info mt-2 mb-2"></div>
        <div class="row">
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Date d'acquisition </span>
                <input id="date" name="date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off"  placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" value="{{ $item->date ? \Carbon\Carbon::parse($item->date)->format("d/m/Y")   : null }}"/>
            </div>
            <div class="col-md-4">
                <span class="text-gray-700 pt-1 fw-semibold fs-6">Venant du demande d'achat   </span>
                <select id="purchase_id" name="purchase_id" class="form-select form-select-sm form-select-solid" data-placeholder="Selectionner ..."  data-control="select2" data-hide-search="false" data-dropdown-parent="#ajax-modal">
                    <option value="0" selected >Aucun</option>
                    @foreach ($purchases as $purchase)
                        <option value="{{ $purchase->id }}" @if ($purchase->id  == $item->purchase_id) selected @endif>N° {{ $purchase->getNumPurchase() . " " .  "(demandé par {$purchase->author->sortname})" }}</option>
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
        <div class="separator border-info mt-2 mb-2"></div>
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
                        {{-- <option value="en_stock" @if($item->etat == 'en_stock') selected @endif >En stock</option> --}}
                        <option value="detruit" @if($item->etat == 'detruit') selected @endif >Détruit</option>
                        <option value="perdu"  @if($item->etat == 'perdu') selected @endif>Perdu</option>
                        <option value="en_panne" @if($item->etat == 'en_panne') selected @endif  >En panne</option>
                    </select>
                </div>
            </div>
        </div>
        
        
        <div class="separator border-info mt-2 mb-2"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Critère 
                        <span  data-bs-toggle="tooltip" data-bs-placement="top" title="Mettez du virgule ',' pour séparer un critére">
                            <i class="fas fa-question-circle"></i>
                        </span>
                        : 
                    </span>
                    <textarea id="propriety" name="propriety" class="form-control form-control form-control-sm form-control-solid" rows="2" data-kt-autosize="true"  placeholder="Ex:Marque, couleur , ..." > {{ $item->propriety }} </textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Observation : </span>
                    <textarea id="observation" name="observation" class="form-control form-control form-control-sm form-control-solid" rows="2" data-kt-autosize="true"  placeholder="Obsevration" > {{ $item->observation }} </textarea>
                </div>
            </div>
            {{-- <div class="col-md-2">

                {{ $item->qrcode }}
            </div> --}}
        </div>
        <div class="separator border-info mt-2 mb-2"></div>
        <div class="row">
            <div class="col-md-4">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Lieu d'emplacement </span>
                    @php
                        $item_locaction  = null; $item_place  = ""; $assigned = [];
                        $place = $item->get_actualy_place_info();
                        if ($place) {
                            $item_locaction  =  $place->location_id;
                            $item_place   =  $place->place;
                            $assigned = explode(",", $place->user_id);
                        }
                    @endphp
                    <select id="location_id" name="location_id" class="form-select form-select-sm form-select-solid" data-placeholder="Lieu d'emplacement"  data-control="select2" data-hide-search="false" data-dropdown-parent="#ajax-modal">
                        @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @if($location->id == $item_locaction) selected @endif >{{ $location->name }}</option>
                      @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Place</span>
                    <div class="input-group input-group-sm">
                        <input type="text" autocomplete="off" class="form-control  form-control-sm form-control-solid" name= "place" value="{{ $item_place  }}" placeholder="Ex : P1"/>
                        <span class="input-group-text" id="location-code"></span>
                      </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Assigné(s) à</span>
                    <select id="user_id" name="user_id[]"   class="form-select form-select-sm form-select-solid" data-placeholder="Assigné(s) à ... "  multiple data-control="select2"  data-dropdown-parent="#ajax-modal">
                        <option value=""  disabled  >Aucun</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @if(in_array($user->id, $assigned)) selected @endif >{{ $user->sortname }}</option>
                        @endforeach
                    </select>
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
    <div class="separator border-info mt-2 mb-2"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Historique d'emplacement (Mouvement) :</span>
                    <table id="historyLocation" class="table table-row-dashed table-row-gray-200 align-middle table-hover "></table>
                </div>
            </div>
            
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
        KTApp.initBootstrapTooltips();
        dataTableInstance.historyLocation = $("#historyLocation").DataTable({
            processing: true,
            ordering: false,
            paging: false,
            dom:"tpr",
            columns: [
                { data: "date",title: 'Date'},
                { data: "location",title: 'Empalcement'},
                { data: "used_by",title: 'En usage'},
            ],
            ajax: {
                url: url("/stock/location/history"),
                data: function(data) {
                    data.item_id = "{{ $item->id }}";
                }
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });
        $("#modal-form-inventor").appForm({
            isModal: false,
            onSuccess: function(response) {
                if (response.row_id) {
                    dataTableUpdateRow(dataTableInstance.invetoryListDataTable, response.row_id,response.data) 
                }
                dataTableInstance.historyLocation.ajax.reload();
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
        $("#location_id").on("change",function(){
            let  location_id = $(this).val();
            getLocationCode(location_id)
        })
        getLocationCode($("#location_id").val());
        function getLocationCode( location_id = 0){
            $.ajax({
                url: url("/stock/get-location-code"),
                type: 'POST',
                dataType: 'json',
                data: {"location_id" : location_id , "_token" : _token},
                success: function(result) {
                    if (result.success) {
                        $("#location-code").text(result.code)
                    }else{
                        toastr.error(result.message);
                    }
                },
                error: function(xhr, status, error) {
                    var err = ("(" + xhr.responseText + ")");
                    toastr.error('Opps !  un erreur se produit. Erreur : '  + err);
                }
            });
        }
        
    });
</script>