<div id="ajax-modal-callback" class="modal-body"></div>
<table id="historic" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover"></table>


<script>
    $(document).ready(function() {
        dataTableInstance.historic = $("#historic").DataTable({
            processing: true,
            dom : "ltpr",
            columns: [{
                    data: "date",
                    title: 'Date',
                    class: 'w-80px'
                },
                {
                    data: "user",
                    title: 'Responsable',
                    class: 'w-150px'
                },
                {
                    data: "input_quantity",
                    title: "Entrée",
                    class: 'w-80px'
                },
                {
                    data: "output_quantity",
                    title: "Consommation",
                    class: 'w-80px'
                },
                {
                    data: "price",
                    title: "Prix Total",
                    class: 'w-80px'
                },
                {
                    data: "actions",
                    searchable: false,
                    orderable: false
                }
            ],
            ajax: {
                url: url("/item-movements/item-detail-data/" + {{ $item->id }}),
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });

        $(document).on("click", ".updateItemMovement", function() {
            let rowID = $(this).data("row_id");
            // alert(rowID);
            // let id = $(this).data("id");
            // let inputQuantity = $("#" + rowID + "input_quantity").val();
            // let outputQuantity = $("#" + rowID + "output_quantity").val();
            // let price = $("#" + rowID + "price").val();
            let data = {
                _token: _token,
                id: $(this).data("id"),
                input_quantity: $("#" + rowID + "input_quantity").val(),
                output_quantity: $("#" + rowID + "output_quantity").val(),
                price: $("#" + rowID + "price").val()
            };
            $.ajax({
                type: 'POST',
                url: url("/item-movements/modal-update"),
                data: data,
                success: function (response) {
                    if (!response.success) {
                        $("#ajax-modal-callback").text(response.message[0]);
                    } else {
                        toastr.success("Modification faite avec  succès");
                        dataTableInstance.historic.ajax.reload();
                        dataTableInstance.itemMovements.ajax.reload();
                        // return true;
                    }
                }
            });
        });
    });
</script>
