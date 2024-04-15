@php
    $mine = isset($mine) ? $mine : true;
@endphp
<div id="message-block-{{ $message->id }}">
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
                    {{-- <div class="symbol symbol-35px symbol-circle">
                    <img alt="Pic" src="{{ $message->sender->avatar_url }}">
                </div> --}}
                    <!--end::Avatar-->
                </div>
                <!--end::User-->
                <div class="d-flex align-items-center mb-2">
                    @include('messaging.modals.reactions.icon-reaction-list', ['message' => $message])
                    <div class="p-5 rounded fw-semibold mw-lg-400px text-end text-light"
                        style="background-color: #087ebf;" data-kt-element="message-text">{{ $message->content }}
                    </div>
                    @include('messaging.modals.more-infos', ['message' => $message])
                </div>
                {!! $message->getViewOfUserReaction() !!}
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
                <div class="d-flex align-items-center mb-2">
                    @include('messaging.modals.reactions.icon-reaction-list')
                    <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start"
                        data-kt-element="message-text">
                        {{ $message->content }}
                    </div>
                    @include('messaging.modals.more-infos', ['message' => $message])
                </div>
                {!! $message->getViewOfUserReaction() !!}
                <!--end::Text-->
            </div>
            <!--end::Wrapper-->
        </div>
    @endif
</div>
