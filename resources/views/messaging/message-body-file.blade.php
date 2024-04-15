@php
    $mine = isset($mine) ? $mine : true;
@endphp
@if (auth()->id() == $message->sender_id && $mine)
    <div class="d-flex justify-content-end mb-10">
        <!--begin::Wrapper-->
        <div class="d-flex flex-column align-items-end">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
                <!--begin::Details-->
                <div class="me-3">
                    <span class="text-muted fs-7 mb-1">{{ $message->created_at->format('H:i') }}, Vous</span>
                    {{-- <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1">You</a> --}}
                </div>
                <!--end::Details-->
                <!--begin::Avatar-->
                <div class="symbol symbol-35px symbol-circle">
                    <img alt="Pic" src="{{ $message->sender->avatar_url }}">
                </div>
                <!--end::Avatar-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-3 rounded bg-secondary text-white mw-lg-400px text-end" data-kt-element="message-text">
                <span class="svg-icon svg-icon-3x svg-icon-primary me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="black"></path>
                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black"></path>
                    </svg>
                </span>
                <a href="{{ url("/message/download/file/$message->id")}}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="title" class="text-gray-800 text-hover-primary">{{ $message->getLimitFilename() }}</a>
            </div>
            <!--end::Text-->
        </div>
        <!--end::Wrapper-->
    </div>
@else
    <div class="d-flex justify-content-start mb-10">
        <!--begin::Wrapper-->
        <div class="d-flex flex-column align-items-start">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
                <!--begin::Avatar-->
                <div class="symbol symbol-35px symbol-circle">
                    <img alt="Pic" src="{{ $message->sender->avatar_url }}">
                </div>
                <!--end::Avatar-->
                <!--begin::Details-->
                <div class="ms-3">
                    <span class="text-muted fs-7 mb-1">{{ $message->sender->sortname }},
                        {{ $message->created_at->format('H:i') }}</span>
                </div>
                <!--end::Details-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-3 rounded bg-secondary text-white mw-lg-400px text-end" data-kt-element="message-text">
                <span class="svg-icon svg-icon-3x svg-icon-primary me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="black"></path>
                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black"></path>
                    </svg>
                </span>
                <a href="{{ url("/message/download/file/$message->id")}}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="title" class="text-gray-800 text-hover-primary">{{ $message->getLimitFilename() }}</a>
            </div>
            <!--end::Text-->
        </div>
        <!--end::Wrapper-->
    </div>
@endif
