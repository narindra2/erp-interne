
@php
    $details = $purchase_model->details;
    $itemsInStock = $purchase_model->itemsInStock;
@endphp
@foreach ($details as $detail)
    <div class="table-responsive">
        <table class="table table-row-bordered">
            <thead>
                <tr class="fw-bold  text-white  bg-info text-center w-100px">
                    <th>{{ $detail->itemType->name }} {{ $detail->quantity }} unité(s) </th>
                    <th>Date d'aquisation</th>
                    <th>Propriété</th>
                    <th>Prix HT</th>
                    <th>Prix HTT</th>
                    <th>N° Facture</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $real_quantity =  $detail->quantity;
                    $item_already_in_stock = $itemsInStock->where("item_type_id"   , $detail->item_type_id)->where("purchase_id" , $detail->purchase_id);
                    $rest_to_migrate_in_stock = $real_quantity - $item_already_in_stock->count();
                @endphp
                @foreach ($item_already_in_stock as $item_in_stock)
                    <tr class="text-center rows-detail-to-stock">
                        <input type="hidden"  name="item_type_id"  value="{{ $item_in_stock->item_type_id }}">
                        <input type="hidden"  name="purchase_id"  value="{{ $item_in_stock->purchase_id }}">
                        <input type="hidden"  name="item_id"  class="item_id" value="{{ $item_in_stock->id }}">
                        <td>
                            <input type="text" class="form-control form-control  form-control-sm"  disabled value="{{ $detail->itemType->name }}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control  form-control-sm date-to-stock" name="date" placeholder="Date d'aquisation" value="{{ $item_in_stock->date ? \Carbon\Carbon::parse($item_in_stock->date)->format("d/m/Y")   :  now()->format("d/m/Y") }}"/>
                        </td>
                        <td>
                            <textarea class="form-control" name="propriety" placeholder=" Ex : Hp, noir, 32 pouces,... " >{{ $item_in_stock->propriety }}</textarea>
                        </td>
                        <td><input type="number" min="50" name="price_ht" step="50" value="{{ $item_in_stock->price_ht }}" class="form-control form-control-sm w-150px" placeholder="Prix HT"/></td>
                        <td><input type="number" min="50" name="price_htt"  step="50" value="{{ $item_in_stock->price_htt }}" class="form-control form-control-sm w-150px" placeholder="Prix HTT"/></td>
                        <td>
                            <select class="form-control form-control-sm  w-150px"  name="num_invoice_id" data-hide-search="true" data-control="select2"   data-placeholder="N° factuere ..."  data-dropdown-parent="#ajax-modal">
                                <option value="null"  selected>Vide</option>
                                @foreach ($purchase_model->numInvoiceLines  as $num)
                                    <option value="{{ $num->id }}" @if ($item_in_stock->num_invoice_id) selected  @endif >{{ $num->num_invoice }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button  title="Migrer dans stock" class="btn btn-sm btn-light-success w-150px save-to-stock" > 
                                <span class="indicator-label">
                                    Mettre  à jour
                                </span>
                                <span class="indicator-progress">
                                    Mise à jour  <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </td>
                    </tr>
                @endforeach


                @for ($i = 1; $i <=   $rest_to_migrate_in_stock;  $i++)
                    <tr class="text-center rows-detail-to-stock">
                        <input type="hidden"  name="item_type_id"  value="{{ $detail->item_type_id ?? null }}">
                        <input type="hidden"  name="purchase_id"  value="{{ $detail->purchase_id }}">
                        <input type="hidden"  name="item_id"  class="item_id" value="">
                        <td>
                            <input type="text" class="form-control form-control  form-control-sm"  disabled value="{{ $detail->itemType->name }}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control  form-control-sm date-to-stock" name="date" placeholder="Date d'aquisation" value="{{ now()->format("d/m/Y") }}"/>
                        </td>
                        <td>
                            <textarea class="form-control" name="propriety" placeholder=" Ex : Hp, noir, 32 pouces,... " >{{ $detail->propriety }}</textarea>
                        </td>
                        <td><input type="number" name="price_ht"  min="50"  step="50" value="{{ $detail->unit_price }}" class="form-control form-control-sm w-150px" placeholder="Prix HT"/></td>
                        <td><input type="number" name="price_htt" min="50"  step="50" class="form-control form-control-sm w-150px" placeholder="Prix HTT"/></td>
                        <td>
                            <select class="form-control form-control-sm  w-150px"  name="num_invoice_id" data-hide-search="true" data-control="select2"   data-placeholder="N° factuere ..."  data-dropdown-parent="#ajax-modal">
                                <option value="null"  selected>Vide</option>
                                @foreach ($purchase_model->numInvoiceLines  as $num)
                                    <option value="{{ $num->id }}" >{{ $num->num_invoice }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button  title="Migrer dans stock" class="btn btn-sm btn-light-info w-150px save-to-stock" > 
                                <span class="indicator-label">
                                    Mettre en stock
                                </span>
                                <span class="indicator-progress">
                                    En cours  <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </td>
                    </tr>
                    @endfor
            </tbody>
        </table>
    </div>
@endforeach
<style>
    #modal-dialog{
        min-width: 1190px;
    }
</style>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        $(".date-to-stock").daterangepicker({
            singleDatePicker: true,
            autoApply: true,
            autoUpdateInput: false,
            locale: {
                defaultValue: "",
                format: 'DD/MM/yyyy',
                applyLabel: "{{ trans('lang.apply') }}",
                cancelLabel: "{{ trans('lang.cancel') }}",
            },
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/yyyy'))
            $(this).change()
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val("")
            $(this).change()
        });
        function releaseBtnSave(btnSave){
            btnSave.attr("data-kt-indicator" , "off")
            btnSave.removeClass("btn-light-info")
            btnSave.addClass("btn-light-success")
            btnSave.attr("disabled" , false)
        }
        /** Save row info article*/ 
        $(".save-to-stock").on("click" , function(){
            let btnSave = $(this);  let data  = {"_token" : _token}
            btnSave.attr("data-kt-indicator" , "on")
            btnSave.attr("disabled" , true)
            btnSave.closest(".rows-detail-to-stock").find(':input, select, textarea').each(function() {
                if ($(this).attr("name")) {
                    data[$(this).attr("name")] = $(this).val()
                }
            });
            $.ajax({
                    url: url("/purchase/migrate-one-article-to-stock"),
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        btnSave.closest(".rows-detail-to-stock").find('.item_id').eq(0).val(result.item_id);
                        btnSave.find('span').eq(0).text("Mettre à jour");
                        releaseBtnSave(btnSave)
                        toastr.clear()
                        toastr.success(result.message);
                        try {
                            dataTableInstance.purchasesTable.ajax.reload();
                        } catch (error) {
                            
                        }
                    },
                    error: function(xhr, status, error) {
                        releaseBtnSave(btnSave)
                        var err = ("(" + xhr.responseText + ")");
                        toastr.error('Opps !  un erreur se produit. Erreur : '  + err);
                    }
                });
        })
    });
</script>