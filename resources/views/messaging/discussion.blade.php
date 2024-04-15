<style>
    .dropdownMessage {
        position: relative;
        display: inline-block;
    }

    .dropdownMessage-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 150px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .dropdownMessage:hover .dropdownMessage-content {
        display: block;
    }
</style>

<div class="card-header" id="kt_chat_messenger_header">
    <div class="card-title">
        <div class="d-flex justify-content-center flex-column me-3">
            <a href="#" class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 mb-2 lh-1">{{ $user->fullname }}</a>
            {{-- <div class="mb-0 lh-1">
                <span class="badge badge-success badge-circle w-10px h-10px me-1"></span>
                <span class="fs-7 fw-bold text-muted">Active</span>
            </div> --}}
        </div>
    </div>
    {{-- <div class="card-toolbar">
        <div class="me-n3">
            <div class="dropdownMessage">
                <span>...</span>
                <div class="dropdownMessage-content">
                    <p>Hello World!</p>
                </div>
              </div>
        </div>
    </div> --}}
</div>

<div class="card-body card-scroll h-300px" id="kt_chat_messenger_body">
    <div id="list-message" class="scroll-y me-n5 pe-5 h-300px h-lg-auto" data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer" data-kt-scroll-wrappers="#kt_content, #kt_chat_messenger_body" data-kt-scroll-offset="5px" style="max-height: 250px;">
        @if ($default_offset == $messages->count())
            <div id="offset_id">
                <div class="d-flex justify-content-center mb-10 text-muted">
                    <p class="text-muted text-hover-primary mb-2" data-offset="{{ $offset }}" id="showPrevious">Afficher les messages précédents</p>
                </div>
            </div>
        @endif
        <div id="messages">
            @foreach ($messages as $message)
            <div class="user-item-message-{{ $message->sender_id }}-{{ $message->receiver_id }}">
                @if ($message->is_file)
                    @include('messaging.message-body-file', ['message' => $message])
                @else
                    @include('messaging.message-body', ['message' => $message])
                @endif
            </div>
        @endforeach
        </div>
    </div>
</div>

<div class="card-footer pt-4" id="kt_chat_messenger_footer">
    <form class="form" id="message-form" action="{{ url('/messaging/send-message') }}" method="POST">
        @csrf
        <input type="hidden" name="receiver_id" value="{{ $user->id }}">
<<<<<<< HEAD
        <textarea class="form-control form-control-solid form-control-flush mb-3" rows="1" name="content" id="content" data-kt-element="input" placeholder="Ecrivez votre message"></textarea>
        <div class="d-flex flex-stack  ">
            <div class="d-flex align-items-center mt-2">
                <input class="form-control form-control-sm form-control-white message-chat-input" name="files[]" type="file" id="message-file-input" multiple>
            </div>
            <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  ">
                @include('partials.general._button-indicator', ['label' => '<i class="fas fa-paper-plane"></i>', "message" => "..."])
            </button>
=======
        <textarea class="form-control form-control-flush mb-3" rows="1" name="content" id="content" data-kt-element="input" placeholder="Ecrivez votre message" required></textarea>
        <div class="d-flex flex-stack">
            <div class="d-flex align-items-center me-2">
                <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="" data-bs-original-title="Coming soon">
                    <i class="bi bi-paperclip fs-3"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="" data-bs-original-title="Coming soon">
                    <i class="bi bi-upload fs-3"></i>
                </button>
            </div>
            <button type="submit" id="submit" class="btn btn-primary mr-2">
                @include('partials.general._button-indicator', ['label' => '<i class="fas fa-paper-plane"></i>', "message" => "..."])
            </button>
            {{-- <button class="btn btn-primary" type="submit" id="submit" data-kt-element="send" ><i class="fas fa-paper-plane"></i></button> --}}
>>>>>>> optimization
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        KTApp.initBootstrapPopovers();
        $(".message-class").last().focus();
        // $(".message-class").last().attr("id", "lastMessage");

        $("#message-form").appForm({
            showAlertSuccess: false,
            onSuccess: function(response) {
                $("#list-message").append(response.view);
                $("#content").val("");
                $("#contact-" + response.contact.contact_id).remove();
                $("#contact-list").prepend(response.contactView);
            }
        });

        $("#showPrevious").on("click", function(e) {
            let offset = $(this).data('offset');
            let userID = {{ $user->id }};
            $.ajax({
                type: "GET",
                url: url("/messaging/discussion/" + userID),
                data: {
                    _token: _token,
                    offset: offset
                },
                success: function (response) {
                    if (response.success) {
                        $("#messages").prepend(response.view);
                        $("#showPrevious").data('offset', response.offset);
                    }
                }
            });
        });
    });
</script>