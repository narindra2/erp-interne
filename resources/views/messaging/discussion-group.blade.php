<div class="card-header" id="kt_chat_messenger_header">
    <div class="card-title">
        <div class="d-flex justify-content-center flex-column me-3">
            <a href="#" class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 mb-2 lh-1">{{ $messageGroup->name }}</a>
            <div class="mb-0 lh-1">
                @php
                    echo modal_anchor(url("/messaging/groups-participants/form/$messageGroup->id"), $nb_participants . " membre(s)", ['title' => 'Membre du canal', 'class' => "fs-7 fw-bold text-muted", 'id' => 'nb_participants', 'data-count' => $nb_participants, 'data-modal-lg' => true]);
                @endphp
                <span class="fs-7 fw-bold text-muted"></span>
            </div>
        </div>
    </div>
</div>

<div class="card-body card-scroll h-300px" id="kt_chat_messenger_body">
    <div id="list-message" class="scroll-y me-n5 pe-5 h-300px h-lg-auto" data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer" data-kt-scroll-wrappers="#kt_content, #kt_chat_messenger_body" data-kt-scroll-offset="5px" style="max-height: 250px;">
        @if ($default_offset == $messages->count())
            {{-- <div>
                <div class="d-flex justify-content-center mb-10 text-muted">
                    <a href="#messageContent" class="text-muted text-hover-primary mb-2" 
                        data-toggle="ajax-tab" data-bs-toggle="tab"
                        data-load-url="{{ url("/messaging/discussion-group/{$messageGroup->id}?offset=$offset") }}">Afficher les messages précédents
                    </a>
                </div>
            </div> --}}
            <div id="offset_id">
                <div class="d-flex justify-content-center mb-10 text-muted">
                    <p class="text-muted text-hover-primary mb-2" data-offset="{{ $offset }}" id="showPrevious">Afficher les messages précédents</p>
                </div>
            </div>
        @endif
        <div id="messages">
            @foreach ($messages as $message)
                <div class="user-item-message">
                    @if ($message->is_file)
                        @include('messaging.message-body-file', ['message' => $message, 'is_group' => true])
                    @else
                        @include('messaging.message-body', ['message' => $message, 'is_group' => true])
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="card-footer pt-4" id="kt_chat_messenger_footer">
    <form class="form" id="message-group-form" action="{{ url('/messaging/send-message') }}" method="POST">
        @csrf
        <input type="hidden" name="group_id" value="{{ $messageGroup->id }}">
        <textarea class="form-control form-control-flush mb-3" rows="1" name="content" id="content" data-kt-element="input" placeholder="Ecrivez votre message"></textarea>
        {{-- <div class="d-flex flex-stack">
            <div class="d-flex align-items-center me-2">
                <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="" data-bs-original-title="Coming soon">
                    <i class="bi bi-paperclip fs-3"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="" data-bs-original-title="Coming soon">
                    <i class="bi bi-upload fs-3"></i>
                </button>
            </div>
            <button class="btn btn-primary" type="submit" id="submit" data-kt-element="send" id="sendMessage"><i class="fas fa-paper-plane"></i></button>
        </div> --}}
        <div class="d-flex flex-stack  ">
            <div class="d-flex align-items-center mt-2">
                <input class="form-control form-control-sm form-control-white message-chat-input" name="files[]" type="file" id="message-file-input" multiple>
            </div>
            <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  ">
                @include('partials.general._button-indicator', ['label' => '<i class="fas fa-paper-plane"></i>', "message" => "..."])
            </button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        $("#message-group-form").appForm({
            showAlertSuccess: false,
            onSuccess: function(response) {
                $("#list-message").append(response.view);
                $("#content").val("");
            }
        });

        $("#showPrevious").on("click", function(e) {
            let offset = $(this).data('offset');
            let messageGroupID = {{ $messageGroup->id }};
            $.ajax({
                type: "GET",
                url: url("/messaging/discussion-group/" + messageGroupID),
                data: {
                    _token: _token,
                    offset: offset
                },
                success: function (response) {
                    if (response.success) {
                        $("#messages").prepend(response.view);
                        ("#showPrevious").data('offset', response.offset);
                    }
                }
            });
        });
    });
</script>