<script>
    $(document).ready(function() {
        $('.table-responsive').on('show.bs.dropdown', function() {
            $('.table-responsive').css("overflow", "absolute");
        });
        $('.table-responsive').on('hide.bs.dropdown', function() {
            $('.table-responsive').css("overflow", "auto");
        })

        $(function() {
            $('[data-bs-toggle="tooltip"]').tooltip({
                container: 'body'
            });
        });
        $("#send-mail-form").appForm({
            isModal: false,
            forceBlock: true,
            onSuccess: function(response) {
                $("#email_content").val("")
                $("#email_object").val("")
            },  
        })
    })
</script>
