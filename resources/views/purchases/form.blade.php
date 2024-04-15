<x-base-layout>
    <div class="tab-content">
        <!--begin::Tab pane-->
        <form action="{{ url('/purchases/save') }}" method="POST" id="purchaseForm">
            @csrf
            <div class="tab-pane fade show active" id="kt_ask" role="tab-panel">
                <!--begin::Product List-->
                <div class="card card-flush py-4 mb-10 flex-row-fluid overflow-hidden">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Les articles qui ont besoin d'être acheté</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-calcul align-middle table-row-dashed fs-6 gy-5 mb-5">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">Article</th>
                                        <th class="min-w-100px">Quantité</th>
                                        <th class="min-w-100px">Prix Unitaire</th>
                                        <th class="min-w-100px">Total</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-bold text-gray-600">
                                    @foreach ($needs as $need)
                                        <tr>
                                            <td>{{ $need->itemType->name }}</td>
                                            <td>{{ $need->qty }}</td>
                                            <td>{{ $need->itemType->unit_price }}</td>
                                            <td>{{ $need->total_price }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="fs-3 text-dark">Prix Total</td>
                                        <td class="text-dark fs-3 fw-boldest">{{ $totalNeedsPrice }}</td>
                                    </tr>
                                </tbody>
                                <!--end::Table head-->
                            </table>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Product List-->
                <!--begin::Orders-->
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
                        <!--begin::Payment address-->
                        <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Informations</h2>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <div class="form-group row py-4" data-select2-id="select2-data-29-iejd">
                                    <label class="col-md-3 col-form-label required">Paiement</label>
                                    <div class="col-md-6" data-select2-id="select2-data-28-fik3">
                                        <select class="form-select form-select-solid select2-hidden-accessible"
                                            name="method" data-hide-search="true" data-control="select2"
                                            data-placeholder="Select an option" data-select2-id="select2-data-4-9h91"
                                            tabindex="-1" aria-hidden="true">
                                            <option value="Carte (VISA)">Carte (VISA)</option>
                                            <option value="Carte (MASTERCARD)">Carte (MASTERCARD)</option>
                                            <option value="Chèque">Chèque</option>
                                            <option value="Espèce">Espèce</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row py-4">
                                    <label class="col-md-3 col-form-label required">Date d'achat</label>
                                    <div class="col-md-4">
                                        <input type="date" name="purchase_date" class="form-control form-control-solid" data-rule-required="true"
                                        data-msg-required="@lang('lang.required')">
                                    </div>
                                </div>

                                <div class="form-group row mb-4">

                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Payment address-->
                        <!--begin::Shipping address-->
                        <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Fichiers joints</h2>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <div class="row row_files">
                                    <div class="col-md-9">
                                        <input type="file" name="files[]" id="" class="form-control form-control-sm my-3" multiple>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Shipping address-->
                    </div>
                    <!--begin::Product List-->
                    <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Les articles</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <div class="d-flex justify-content-end">
                                <p id="addLine" class="btn btn-light-primary"><i class="fas fa-plus-circle fs-4"></i></p>
                            </div>
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-calcul align-middle table-row-dashed fs-6 gy-5 mb-5">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="min-w-150px">Article</th>
                                            <th class="min-w-100px">Quantité</th>
                                            <th class="min-w-100px">Unité</th>
                                            <th class="min-w-100px">Prix Unitaire</th>
                                            <th class="min-w-100px">Total</th>
                                            <th class="min-w-100px"></th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-bold text-gray-600">
                                        <tr class="add_tr">
                                            <!--begin::Product-->
                                            <td>
                                                <select class="form-control form-control-solid" id="itemType" name="item_type_id[]">
                                                    @foreach ($itemTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <!--end::Product-->
                                            <!--begin::Quantity-->
                                            <td class="text-end">
                                                <input type="number"
                                                    class="form-control form-control-solid w-100px calcul quantity"
                                                    name="quantity[]" min="1" value="1">
                                            </td>
                                            <!--end::Quantity-->
                                            <!--begin::Quantity-->
                                            <td>
                                                <select class="form-control form-control-solid" id="unitItem" name="unit_item_id[]">
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <!--end::Quantity-->
                                            <!--begin::Price-->
                                            <td class="text-end">
                                                <input type="number"
                                                    class="form-control form-control-solid w-100px calcul unitPrice"
                                                    name="unit_price[]" min="0" value="0">
                                            </td>
                                            <!--end::Price-->
                                            <!--begin::Total-->
                                            <td class="total">0</td>
                                            <td class="text-center"><button
                                                    class="btn btn-light-danger font-weight-bold mr-2 btn-sm"
                                                    onclick="deleteLine(this)"><i
                                                        class="far fa-trash-alt fs-3"></i></button></td>
                                            <!--end::Total-->
                                        </tr>
                                        <!--end::Products-->
                                        <!--begin::Grand total-->
                                        <tr>
                                            <td colspan="3" class="fs-3 text-dark">Prix Total</td>
                                            <td class="text-dark fs-3 fw-boldest" id="totalPrice">0</td>
                                        </tr>
                                        <!--end::Grand total-->
                                    </tbody>
                                    <!--end::Table head-->
                                </table>
                                <!--end::Table-->
                                <div class="d-flex justify-content-end mt-5">
                                    <button
                                        class="btn btn-light-success font-weight-bold mr-2 btn-sm">Sauvegarder</button>
                                </div>
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Product List-->
                </div>
                <!--end::Orders-->
            </div>
            <!--end::Tab pane-->
        </form>
    </div>

    @section('scripts')
        <script>
            let min = 1;

            function deleteLine(content) {
                if (min > 1) {
                    min--;
                    $(content).closest(".add_tr").remove();
                }
            }

            $(document).ready(function() {
                KTApp.initSelect2();

                $("#purchaseForm").appForm({
                    isModal:false,
                    onSuccess:function(response){
                        window.location.replace("{{ url("/purchases") }}");
                    }
                });

                $("#addLine").on("click", function() {
                    min++;
                    $(".add_tr").eq(0).clone().insertAfter(".add_tr:last");
                });

                $(document).on("keyup change", ".calcul", () => {
                    let totalPrice = 0;
                    $(".add_tr").each((i) => {
                        let quantity = $(".add_tr").eq(i).find(".quantity").val();
                        let unitPrice = $(".add_tr").eq(i).find(".unitPrice").val();
                        let totalOneLine = parseFloat(quantity) * parseFloat(unitPrice);
                        $(".add_tr").eq(i).find(".total").text(totalOneLine);
                        totalPrice += totalOneLine;
                    });
                    $("#totalPrice").text(totalPrice);
                });
            });
        </script>
    @endsection
</x-base-layout>
