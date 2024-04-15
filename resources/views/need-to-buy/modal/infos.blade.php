<form action="{{ url("/save-detail-need") }}" method="POST" id="formDetail">
    @csrf
    <div class="card-body">
        <input type="hidden" name="need_to_buy_id" value="{{ $needToBuy->id }}">
        <input type="hidden" name="item_type_id" value="{{ $needToBuy->item_type_id }}">
        <input type="hidden" name="department_id" value="{{ $needToBuy->department_id }}">
        <div class="form-group row mb-5">
            <div class="col-md-2">
                <input type="number" placeholder="Quantité" name="qty" id="qty" class="form-control form-control-sm form-control-solid" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
            </div>
            <div class="col-md-4">
                <select class="form-control form-control-sm form-control-solid" name="unit_item_id" id="" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                    <option>--  Unité --</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-control form-control-sm form-control-solid" name="status" id="" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
                    <option>--  Statut --</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-light-primary" id="submit">Sauvegarder</button>
            </div>
        </div>
        <div class="form-group mb-5">
            <table id="needToBuyInfos" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover"></table>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {

        dataTableInstance.needToBuyInfos = $("#needToBuyInfos").DataTable({
            processing: true,
            dom: "tpr",
            columns: [{
                    data: "status_date",
                    title: "Date"
                },
                {
                    data: "qty",
                    title: 'Quantité'
                },
                {
                    data: "unit",
                    title: 'Unité'
                },
                {
                    data: "status",
                    title: 'Statut'
                },
                {
                    data: "author",
                    title: 'Auteur'
                }
            ],
            ajax: {
                url: url("/need-to-buy/detail/{{ $needToBuy->id }}"),
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });

        $("#formDetail").appForm({
            isModal: false,
            onSuccess: function(response) {
                dataTableaddRowIntheTop(dataTableInstance.needToBuyInfos, response.data)
            }
        });
    });
</script>
