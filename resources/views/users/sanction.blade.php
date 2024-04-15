<div class="card shadow-sm">
    <div class="card-body">
        <div class="form-group row py-4">
            <label  class="col-md-2 col-form-label">@lang('lang.verbal_warning')</label>
            <div class="col-md-3">
                <input disabled class="form-control form-control-transparent" autocomplete="off" type="text" value="{{ $user->verbal_warning }}" name="verbal_warning" />
            </div>
        </div>
        <div class="form-group row py-4">
            <label  class="col-md-2 col-form-label">@lang('lang.written_warning')</label>
            <div class="col-md-3">
                <input disabled class="form-control form-control-transparent" autocomplete="off" type="text" value="{{ $user->written_warning }}" name="verbal_warning" />
            </div>
        </div>
        <div class="form-group row py-4">
            <label  class="col-md-2 col-form-label">@lang('lang.layoff')</label>
            <div class="col-md-3">
                <input disabled class="form-control form-control-transparent" autocomplete="off" type="text" value="{{ $user->layoff }}" name="layoff" />
            </div>
        </div>

        <table id="sanctionTable" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
    </div>
</div>

<script>
    $(document).ready(function () {
        let userId = {{ $user->id }};
        dataTableInstance.sanctionTable = $("#sanctionTable").DataTable({
            processing: true,
            columns: [ 
                {data :"date" , title: 'Date'},
                {data :"reason" , title: 'Motif'},
                {data: "type", title: 'Type'},
                {data: "duration", title: 'Durée (en jour)'}
            ],
            ajax: {
                url: url("/users/sanctions-data/" + userId),
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });
    });
</script>