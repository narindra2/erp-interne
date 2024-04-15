<style class="style-script-message-modal">
    .card-message {
        position: fixed;
        bottom: 0;
        right: 0;
        width: 450px;
        z-index: 10;
    }

    .modal-header-dark {
        background-color: darkslategrey;
    }

    .header-discussion {
        height: 50px;
        background-color: #505089;
    }

</style>

<div class="card-message" data-contact_id="{{ $contact->id }}">
    <div class="card shadow-sm">
        <div class="header-discussion">
            <div class="row">
                <div class="col-10">
                    <h5 class="mt-4 mx-4" style="color: white">{{ $contact->sortname }}</h5>
                </div>
                <div class="col-2 text-end">
                    <i class="fas fa-backspace fs-3 mt-4 px-3" id="hideDiscussion"></i>
                </div>
            </div>
        </div>
        <div id="message-body" class="card-body card-scroll h-400px">
            <div id="offset_id">
                <div class="d-flex justify-content-center mb-10 text-muted">
                    <p class="text-muted text-hover-primary mb-2" data-offset="{{ $offset }}" id="showPreviousModal">Afficher les messages précédents</p>
                </div>
            </div>
            <div id="messagesModal">
                @foreach ($messages as $message)
                    @if ($message->is_file)
                        @include('messaging.message-body-file', ['message' => $message])
                    @else
                        @include('messaging.message-body', ['message' => $message])
                    @endif
                @endforeach
            </div>
        </div>
        <form id="message-form-modal" action="{{ url('/messaging/send-message') }}" method="post">
        <div class="card-footer">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $contact->id }}">
                <input class="form-control form-control-sm form-control-white message-chat-input" name="files[]" type="file" id="message-file-input" multiple>
                <div class="row mt-3">
                    <div class="col-9">
                        <textarea rows="1" name="content" id="content" class="form-control form-control-solid ml-4" data-kt-autosize="true" placeholder="Votre message"></textarea>
                    </div>
                    <div class="col-2">
                        <button type="submit" id="sendMessage" class=" btn btn-light-primary  mr-2">
                            @include('partials.general._button-indicator', ['label' => '<i class="fas fa-paper-plane"></i>',"message" => "..."])
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script class="style-script-message-modal">
    $(document).ready(function () {
        
        $('[data-bs-toggle="tooltip"]').tooltip({
            container: 'body'
        });

        $("#hideDiscussion").on("click", function() {
            $(".card-message").remove();
            $(".style-script-message-modal").remove();
        })

        $("#showPreviousModal").on("click", function(e) {
            let offset = $(this).data('offset');
            let userID = "{{ $contact->id }}";
            $.ajax({
                type: "GET",
                url: url("/messaging/discussion/" + userID),
                data: {
                    _token: _token,
                    offset: offset
                },
                success: function (response) {
                    if (response.success) {
                        $("#messagesModal").prepend(response.view);
                        $("#showPreviousModal").data('offset', response.offset);
                    }
                }
            });
        });
        $("#message-form-modal").appForm({
            submitBtn : "#sendMessage",
            showAlertSuccess: false,
            onSuccess: function(response) {
                $("#messagesModal").append(response.view);
                $("#content").val("");
            }
        });
    });
</script>
