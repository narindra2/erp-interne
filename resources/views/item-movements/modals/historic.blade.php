<div class="card shadow-sm">
    <div class="card-body">
        <table id="historic" data-item_id="{{ $item->id }}" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover ">
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        dataTableInstance.historic = $("#historic").DataTable({
            processing: true,
            dom: "ltpr",
            columns: [{
                    data: "date",
                    title: 'Date'
                },
                {
                    data: "location",
                    title: "Lieu"
                },
                {
                    data: "local",
                    title: "Local"
                },
                {
                    data: "users",
                    title: "Utilisateur(s)"
                }
            ],
            ajax: {
                url: url("/item-movements/item-historic-data/" + $("#historic").data("item_id")),
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });
    });
</script>
