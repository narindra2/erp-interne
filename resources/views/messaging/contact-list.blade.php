<!--begin::Sidebar-->
<div class="flex-column flex-lg-row-auto w-100 w-lg-300px w-xl-400px mb-10 mb-lg-0">
    <!--begin::Contacts-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-7" id="kt_chat_contacts_header">
            <!--begin::Form-->
            <form class="w-100 position-relative" autocomplete="off">
                <!--begin::Icon-->
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span
                    class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                            transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                        <path
                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                            fill="currentColor"></path>
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <!--end::Icon-->
                <select id="user-list" class="form-select form-select-sm form-select-solid" data-placeholder="Chercher d'autres personnes">
                    <option></option>
                </select>
            </form>
            <!--end::Form-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-5">
            <!--begin::List-->
            <div id="contact-list-group" class="scroll-y me-n5 pe-5 h-200px h-lg-auto" data-kt-scroll="true"
                data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_contacts_header"
                data-kt-scroll-wrappers="#kt_content, #kt_chat_contacts_body" data-kt-scroll-offset="5px"
                style="max-height: 397px;">
                <h4 class="text-center">
                    Vos canaux 
                    @php
                        echo modal_anchor(url("/messaging/groups/form"), '<i class="far fa-plus-square fs-3 text-primary mx-5"></i>', ['title' => 'Nouveau canal']);
                    @endphp
                </h4>
                <div id="group_list">
                    @foreach ($groups as $group)
                        @include('messaging.group-item', ['group' => $group])
                    @endforeach
                </div>
            </div>
            <hr>
            <div id="contact-list" class="scroll-y me-n5 pe-5 h-200px h-lg-auto" data-kt-scroll="true"
                data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_contacts_header"
                data-kt-scroll-wrappers="#kt_content, #kt_chat_contacts_body" data-kt-scroll-offset="5px"
                style="max-height: 397px;">
                @foreach ($contacts as $contact)
                    @include('messaging.contact-item', ['contact' => $contact])
                @endforeach
            </div>
            <!--end::List-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Contacts-->
</div>
<!--end::Sidebar-->

@section('scripts')

<script>
    $(document).ready(function () {

        $('#user-list').select2({
            ajax: {
                url: url("/messaging/search-user"),
                type: "get",
                data: function (params) { 
                    return {
                        searchTerm: params.term
                    }
                },
                processResults: function(response) {
                    return {
                        results: response
                    }
                },
                cache: true
            },
        });

        $("#user-list").change(function() {
            let userIDSelected = $(this).val();

            $.ajax({
                type: "GET",
                url: url("/messaging/discussion/" + userIDSelected),
                data: {
                    _token: _token
                },
                dataType: "html",
                success: function (response) {
                    $("#messageContent").html(response);
                    console.log(response);
                }
            });
        });
    });
</script>

@endsection
