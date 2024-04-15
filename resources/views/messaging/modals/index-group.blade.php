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

<div class="card-message" data-group_id="{{ $messageGroup->id }}">
    <div class="card shadow-sm">
        <div class="header-discussion">
            <div class="row">
                <div class="col-10">
                    {{-- <h5 class="mt-4 mx-4" style="color: white">{{ $messageGroup->name }}</h5> --}}
                    <h5 class="mt-4 mx-4" style="color: white">
                        @php
                            echo modal_anchor(url("/messaging/group-participants-modal/$messageGroup->id"), $messageGroup->name, ["title" => "Les participants", 'data-modal-lg' => true, 'class' => 'text-light']);
                        @endphp
                    </h5>
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
                {{-- <div class="d-flex flex-column mb-5 align-items-start">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40 mr-3">
                            <img alt="Pic" src="/metronic/theme/html/demo1/dist/assets/media/users/300_12.jpg">
                        </div>
                        <div>
                            <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">Matt Pears</a>
                            <span class="text-muted font-size-sm">2 Hours</span>
                        </div>
                    </div>
                    <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">How likely are you to recommend our company to your friends and family?</div>
                </div> --}}
            </div>
        </div>
        <div class="card-footer">
            <form id="message-form-modal" action="{{ url('/messaging/send-message') }}" method="post">
                @csrf
                <input type="hidden" name="group_id" value="{{ $messageGroup->id }}">
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
            </form>
            
        </div>
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

        $("#message-form-modal").appForm({
            submitBtn : "#sendMessage",
            showAlertSuccess: false,
            onSuccess: function(response) {
                $("#messagesModal").append(response.view);
                $("#content").val("");
            }
        });

        $("#showPreviousModal").on("click", function(e) {
            let offset = $(this).data('offset');
            let groupID = "{{ $messageGroup->id }}";
            $.ajax({
                type: "GET",
                url: url("/messaging/discussion-group/" + groupID),
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
    });
</script>
