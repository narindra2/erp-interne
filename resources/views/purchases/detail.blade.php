<x-base-layout>
    <div class="tab-content">
        <!--begin::Tab pane-->
        <div class="tab-pane fade show active" id="kt_ecommerce_sales_order_summary" role="tab-panel">
            <!--begin::Orders-->
            <div class="d-flex flex-column gap-7 gap-lg-10">
                <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
                    <!--begin::Payment address-->
                    <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                        <!--begin::Background-->
                        <div class="position-absolute top-0 end-0 opacity-10 pe-none text-end">
                            <img src="/metronic8/demo1/assets/media/icons/duotune/ecommerce/ecm001.svg"
                                class="w-175px">
                        </div>
                        <!--end::Background-->
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
                                <label class="col-md-3 col-form-label">{{ $purchase->method }}</label>
                            </div>

                            <div class="form-group row py-4">
                                <label class="col-md-3 col-form-label required">Date d'achat</label>
                                <label class="col-md-3 col-form-label">{{ $purchase->dateHTML() }}</label>
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Payment address-->
                    <!--begin::Shipping address-->
                    {{-- <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                        <!--begin::Background-->
                        <div class="position-absolute top-0 end-0 opacity-10 pe-none text-end">
                            <img src="/metronic8/demo1/assets/media/icons/duotune/ecommerce/ecm006.svg"
                                class="w-175px">
                        </div>
                        <!--end::Background-->
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Fichiers joints</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">Unit 1/23 Hastings Road,
                            <br>Melbourne 3000,
                            <br>Victoria,
                            <br>Australia.
                        </div>
                        <!--end::Card body-->
                    </div> --}}
                    <!--end::Shipping address-->
                </div>
                <!--begin::Product List-->
                <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2 id="addLine">Les articles</h2>
                            <div class="row">
                            </div>
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
                                        <th class="min-w-100px">Unité</th>
                                        <th class="min-w-100px">Prix Unitaire</th>
                                        <th class="min-w-100px">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-bold text-gray-600">
                                    @foreach ($purchase->details as $detail)
                                        <tr>
                                            <td>{{ $detail->itemType->name }}</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ $detail->unit ? $detail->unit->name : '-' }}</td>
                                            {{-- <td>{{ $detail->unit->name }}</td> --}}
                                            <td>{{ $detail->unit_price }}</td>
                                            <td>{{ $detail->total_price }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="fs-3 text-dark">Prix Total</td>
                                        <td class="text-dark fs-3 fw-boldest">{{ $purchase->total_price }}</td>
                                    </tr>
                                    <!--end::Grand total-->
                                </tbody>
                                <!--end::Table head-->
                            </table>
                            <!--end::Table-->
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Product List-->
            </div>
            <!--end::Orders-->
        </div>
        <!--end::Tab pane-->
    </div>
</x-base-layout>
