
@php
    $details = $purchase_model->details;
    // dump($purchase_model);
@endphp
    {{-- <ul class="nav nav-tabs nav-pills flex-row border-0 flex-md-column me-5 mb-3 mb-md-0 fs-6 min-w-lg-" role="tablist">
        @foreach ($details as $detail)
            <li class="nav-item w-100 me-0 mb-md-2" role="presentation">
                <a class="nav-link w-100 btn btn-flex btn-active-light-info {{ $loop->index == 0 ? 'active ' : '' }}" data-bs-toggle="tab" href="#tab-{{ $loop->index + 1 }}" aria-selected="true" role="tab">
                    <i class="ki-duotone ki-icons/duotune/general/gen001.svg fs-2 text-info me-3"></i>                        <span class="d-flex flex-column align-items-start">
                        <span class="fs-7">#{{  $detail->itemType->name }}</span>
                    </span>
                </a>
            </li>
            <div class="separator border-info"></div> 
        @endforeach
    </ul> --}}

    {{-- <div class="tab-content" id="myTabContent"> --}}
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
                    <tbody >
                        @for ($i = 1; $i <=   $detail->quantity; $i++)
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
                                <td><input type="number" min="50" name="price_ht" step="50" value="{{ $detail->unit_price }}" class="form-control form-control-sm w-150px" placeholder="Prix HT"/></td>
                                <td><input type="number" min="50" name="price_htt"  step="50" class="form-control form-control-sm w-150px" placeholder="Prix HTT"/></td>
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
                        releaseBtnSave(btnSave)
                        toastr.clear()
                        toastr.success(result.message);
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