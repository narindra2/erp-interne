<div>
    @foreach ($groups as $group)
        @include('messaging.modals.contact-group', ['group' => $group])
    @endforeach
</div>

<script>
    $(document).ready(function () {
        $(".discussionGroupModal").on("click", function() {
            $.post(
                url("/messaging/show-modal-message"), 
                {
                    _token: _token,
                    group_id: $(this).data('id'),
                },
                function (data, textStatus, jqXHR) {
                    $(".card-message").remove()
                    $("#kt_content").append(data.view)
                }
            );
        })
    });
</script>