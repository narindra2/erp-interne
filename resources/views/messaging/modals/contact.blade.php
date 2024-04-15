<div class="d-flex flex-stack py-4">
    <!--begin::Details-->
    <div class="d-flex align-items-center" id="discussionModal{{ $contact->user->id }}" data-id="{{ $contact->user->id }}">
        <!--begin::Avatar-->
        <div class="symbol symbol-45px symbol-circle">
            <span class="symbol-label bg-light-danger text-danger fs-6 fw-bolder">@php echo $contact->user->sortname[0] @endphp</span>
        </div>
        <!--end::Avatar-->
        <!--begin::Details-->
        <div class="ms-5">
            <a href="#" style="pointer:cursor">{{ $contact->user->sortname }}</a>
            <div class="fw-bold text-muted">{{ $contact->content }}</div>
        </div>
        <!--end::Details-->
    </div>
    <!--end::Details-->
    <!--begin::Lat seen-->
    <div class="d-flex flex-column align-items-end ms-2">
        <span class="text-muted fs-7 mb-1">{{ getDateHuman($contact->created_at) }}</span>
    </div>
    <!--end::Lat seen-->
</div>
<!--end::User-->
<!--begin::Separator-->
<div class="separator separator-dashed d-none"></div>
<!--end::Separator-->

<script>
    $(document).ready(function () {
        let discussionModal = "#discussionModal" + @php echo $contact->user->id @endphp;
        var r = null;
        $(discussionModal).on("click", function() {
            if (r != null) {
                r.abort();
            }
            r = $.post(
                url("/messaging/show-modal-message"), 
                {
                    _token: _token,
                    sender_id: $(this).data('id')
                },
                function (data, textStatus, jqXHR) {
                    $(".card-message").remove()
                    $("#kt_content").append(data.view)
                }
            );
        })
    });
</script>