<div class="card-body py-5">
    <table id="checkHistory" class="table table-striped gy-7 gs-7 table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
</div>
<script>
    $(document).ready(function() {
        dataTableInstance.checkHistory = $("#checkHistory").DataTable({
                    processing: true,
                    dom : "tpr",
                    ordering : false,
                    columns: [ 
                        { data : "user" , title : 'Vous'},
                        { data : "check_event" , title : 'Evennement',"class":"text-right"},
                        { data : "date_time" , title : 'Date et heure',"class":"text-right"},
                    ],
                    ajax: {
                        url: url("/user/check/history"),
                    },
                });
    })
</script>