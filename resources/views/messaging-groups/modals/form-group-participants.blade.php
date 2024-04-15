<div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
    <!--begin::Content-->
    <div class="text-center mb-13">
        <h1 class="mb-3">{{ $messageGroup->name }}</h1>
        {{-- <div class="text-muted fw-semibold fs-5">Invite Collaborators To Your Project</div> --}}
    </div>
    <!--end::Content-->
    <!--begin::Search-->
    <div>
        @if ($action)    
            <select id="participants-select"
                data-dropdown-parent="#ajax-modal" 
                class="form-select form-select-sm form-select-solid" 
                data-placeholder="Chercher d'autres personnes"
                data-control="select2"
                data-allow-clear="true"
                data-ajax--url = {{ url("/messaging/search-user") }}
                data-ajax--cache = "true"
                data-minimum-input-length = "2">
                <option></option>
            </select>
        @endif
        <input type="hidden" id="message_group_id" name="message_group_id" value="{{ $messageGroup->id }}">
        <!--begin::Wrapper-->
        <div class="py-5">
            <!--begin::Suggestions-->
            <div data-kt-search-element="suggestions">
                <!--begin::Heading-->
                <h3 class="fw-semibold mb-5">Les membres:</h3>
                <!--end::Heading-->
                <!--begin::Users-->
                <div class="mh-375px scroll-y me-n7 pe-7" id="participant-list">
                    @foreach ($participants as $participant)
                        @include('messaging-groups.modals.participant', ['participant' => $participant, 'action' => $action])
                    @endforeach
                </div>
                <!--end::Users-->
            </div>
            <!--end::Empty-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Search-->
</div>

<script>
    $(document).ready(function () {
        KTApp.initSelect2();
        $("#participants-select").change(function() {
            let userIDSelected = $(this).val();
            $.ajax({
                type: "POST",
                url: url("/messaging/groups-participants/save-form"),
                data: {
                    _token: _token,
                    user_id: userIDSelected,
                    message_group_id: $("#message_group_id").val()
                },
                success: function (response) {
                    if (response.success) {
                        $("#participant-list").append(response.view)
                        incrementParticipantCount();
                    }
                }
            });
        });

        let incrementParticipantCount = function () {
            let count = $("#nb_participants").data("count") + 1;
            $("#nb_participants").text(count + " membre(s)");
            $("#nb_participants").data('count', count);
        }

        $(".deleteParticipant").on("click", function() {
            let participantID = $(this).data('participant');
            $.ajax({
                type: "POST",
                url: url("/messaging/groups-participants/delete-participant/" + participantID),
                data: {
                    _token: _token
                },
                success: function (response) {
                    $("#participant-" + participantID).remove();
                    let count = $("#nb_participants").data("count") - 1;
                    $("#nb_participants").text(count + " membre(s)");
                    $("#nb_participants").data('count', count);
                }
            });
        });
    });
</script>