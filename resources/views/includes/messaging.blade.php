<script>
    $(document).ready(function() {

        $(document).on("click", ".make-reaction-to-message", function() {
            let messageId = $(this).data('message_id')
            let reactionId = $(this).data('reaction_id')
            $.post(url("/messaging/reactions/save-reaction"), {
                    _token: _token,
                    message_id: messageId,
                    message_reaction_id: reactionId
                },
                function(data, textStatus, jqXHR) {
                    if (data.success) {
                        let count = 0
                        let target = `#user-reaction-${messageId}-${reactionId}`
                        if ($(target).length) {
                            count = parseInt($(target).text()) + data.addition
                            if (count <= 0) $(target).parent().remove()
                            else $(target).text(count)
                        } else {
                            $(`#user-reaction-block-${messageId}`).append(data.view)
                        }
                    }
                },
            );
        });

        $(document).on("click", ".count-reaction", function() {
            alert($(this).data('message_id') + ' ' + $(this).data('reaction_id'));
        });
    });
</script>