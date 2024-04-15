<style>
    .loader {
        border: 6px solid #f3f3f3;
        /* Light grey */
        border-top: 6px solid #3498db;
        /* Blue */
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<!--begin::Menu-->
<div class="menu menu-sub menu-sub-dropdown menu-column w-250px w-lg-325px" data-kt-menu="true">
    <!--begin::Heading-->
    <div class="d-flex flex-column flex-center bgi-no-repeat rounded-top px-9 py-10"
        style="background-image:url('{{ asset(theme()->getMediaUrlPath() . 'misc/pattern-1.jpg') }}')">
        <!--begin::Title-->
        <h3 class="text-white fw-bold mb-3">
            Messagerie
        </h3>
        <!--end::Title-->
    </div>
    <!--end::Heading-->

    <div class="mt-5 mx-5">
        <div class="d-flex justify-content-end">
            @php
                echo modal_anchor(url("/messaging/search-discussion"), "+ discussion", ["title" => "Chercher une discussion", "data-modal-lg" => true, "class"=> "btn btn-sm btn-light-info"]);
            @endphp
        </div>
        <div id="contact-list" class="scroll-y me-n5 pe-5 h-200px h-lg-auto" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
            data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_contacts_header"
            data-kt-scroll-wrappers="#kt_content, #kt_chat_contacts_body" data-kt-scroll-offset="5px"
            style="max-height: 397px;">
            <div class="d-flex justify-content-center">
                <div class="loader"></div>
            </div>
        </div>
    </div>

    <!--begin::View more-->
    <div class="py-2 text-center border-top">
        Voir plus
        {!! theme()->getSvgIcon('icons/duotune/arrows/arr064.svg', 'svg-icon-5') !!}
    </div>
    {{-- <div class="py-2 text-center border-top">
        <a href="{{ url('/messaging') }}" class="btn btn-color-gray-600 btn-active-color-primary">
            Voir plus
            {!! theme()->getSvgIcon('icons/duotune/arrows/arr064.svg', 'svg-icon-5') !!}
        </a>
    </div> --}}
    <!--end::View more-->
</div>
<!--end::Menu-->

<script>
    $(document).ready(function() {
        $("#modalMessage").on("click", function() {
            $.get(url("/messaging/modals/view-contacts"), {
                    _token: _token
                },
                function(data, textStatus, jqXHR) {
                    $("#contact-list").empty()
                    $("#contact-list").html(data.view)
                },
            );
        })
    });
</script>
