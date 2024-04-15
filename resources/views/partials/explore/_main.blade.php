{{-- @php 
    $messages = getMessagesNotSeen(auth()->user());
    $messageGroups = getMessagesNotSeenGroup(auth()->user());
@endphp 

<a id="private-chat" class="btn btn-sm position-fixed zindex-2 top-75 mt-50 end-0 transform-90 fs-6 me-2">
    @foreach ($messages as $message)
        @if ($message->sender_id != null)
            @include('messaging.bubbles.bubble', ['message' => $message])
        @endif
    @endforeach

    @foreach ($messageGroups as $messageGroup)
        @include('messaging.bubbles.bubble-group', ['messageGroup' => $messageGroup])
    @endforeach
</a>
 
 <script>
    $(document).ready(function () {
        $(document).on("click", ".chat-modal", function() {
            let sender_id = $(this).data('id')
            $.post(
                url("/messaging/show-modal-message"), 
                {
                    _token: _token,
                    sender_id: sender_id
                },
                function (data, textStatus, jqXHR) {
                    $("#chat-private-id-notification-count-" + sender_id).text("")
                    $(".card-message").remove()
                    $("#kt_content").append(data.view)
                }
            );
        });

        $(document).on("click", ".chat-group-modal", function() {
            let group_id = $(this).data('id')
            $.post(
                url("/messaging/show-modal-message"), 
                {
                    _token: _token,
                    group_id: group_id
                },
                function (data, textStatus, jqXHR) {
                    $("#chat-private-id-notification-group-count-" + group_id).text("")
                    $(".card-message").remove()
                    $("#kt_content").append(data.view)
                }
            );
        })
    });
</script>  --}}