<div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">

    <!--begin::Wrapper-->
    <div class="py-5">
        <!--begin::Suggestions-->
        <div data-kt-search-element="suggestions">
            <!--begin::Heading-->
            <h3 class="fw-semibold mb-5">Les membres:</h3>
            <!--end::Heading-->
            <!--begin::Users-->
            <div class="mh-375px scroll-y me-n7 pe-7">
                <table id="userDiscussion" class="table table-striped table-row-bordered gy-5 gs-7">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Nom et prénom(s)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="symbol symbol-35px symbol-circle">
                                        <img alt="Pic" src="{{ $user->avatar_url }}">
                                    </div>
                                </td>
                                <td>{{ $user->fullname }}</td>
                                <td><button class="btn btn-primary btn-sm show-modal-message" data-bs-dismiss="modal" data-id="{{ $user->id }}">Voir <i class="fas fa-paper-plane mx-1"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!--end::Users-->
        </div>
        <!--end::Empty-->
    </div>
    <!--end::Wrapper-->
</div>

<script>
    $(document).ready(function() {
        $("#userDiscussion").DataTable({})

        $(document).on("click", ".show-modal-message", function() {
            let id = $(this).data('id')
            $.post(
                url("/messaging/show-modal-message"), 
                {
                    _token: _token,
                    sender_id: id
                },
                function (data, textStatus, jqXHR) {
                    $(".card-message").remove()
                    $("#kt_content").append(data.view)
                }
            );
        });
    });
</script>
