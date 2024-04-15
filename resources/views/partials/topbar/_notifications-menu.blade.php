<div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
    <div class="d-flex flex-column bgi-no-repeat rounded-top"
        style="background-image:url('{{ asset(theme()->getMediaUrlPath() . 'misc/pattern-1.jpg') }}')">
        <h3 class="text-white fw-bold px-9 mt-10 mb-6">
            Notifications
        </h3>
        <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-bold px-9">
            <li class="nav-item">
                <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab"
                    href="#topbar_notifications_1">info</a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_3">Logs</a>
            </li> --}}
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="topbar_notifications_1" role="tabpanel">
            <div class="scroll-y mh-325px my-5 px-8">
                <div id="notification-item">
                    @php
                        $unread = "";
                    @endphp
                    @foreach ($notifications as $notification)
                        @php
                            if (!$notification->read_at) {
                                $unread = $unread . "," . $notification->id;
                            }
                        @endphp

                        {!! view('notifications.template', ['notification' => $notification])->render() !!}
                    @endforeach
                </div>
            </div>
            {{-- <div class="py-3 text-center border-top">
                <a href="{{ theme()->getPageUrl('pages/profile/activity') }}"class="btn btn-color-gray-600 btn-active-color-primary">
                    View All
                    {!! theme()->getSvgIcon('icons/duotune/arrows/arr064.svg', 'svg-icon-5') !!}
                </a>
            </div> --}}
        </div>
        <div class="text-center mb-3">
            <input type="hidden" name="offset" id="offest-notification"  value= {{ $take }} >
            <button type="submit" id="load-more-notification" class=" text-muted btn-sm btn btn-active-light mt-2">
                @include('partials.general._button-indicator', ['label' => trans('lang.load_more') ,"message" => "..."])
            </button>
        </div>
       <input type="hidden" id="unread_notification" value="{{ $unread }}">
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".notification-item").on("click", function() {
            var target =  $(this)
            var notificationId =target.attr("data-notification-id")
            var notificationIsUnread = target.attr("data-notification-is-unread")
            if (notificationIsUnread) {
                $.ajax({
                    url: url("/notification/set/seen"),
                    type: 'POST',
                    dataType: 'json',
                    data: {"_token" : _token ,"id" : notificationId},
                    success: function(response) {
                        target.replaceWith(response.data)
                    },
                });
            }

        })
       $("#load-more-notification").on("click", function() {
            var target =  $(this)
                target.attr("data-kt-indicator", "on");
            if (1) {
                $.ajax({
                    url: url("/load/more/notification"),
                    type: 'POST',
                    dataType: 'json',
                    data: {"_token" : _token ,"offset" : $("#offest-notification").val()},
                    success: function(notification) {
                        console.log(notification);
                        if (notification.success) {
                            $("#offest-notification").val(notification.offset)
                            if ($(".notification-item").length) {
                                $(".notification-item:last").after(notification.item)
                            } else {
                                $("#notification-item").html(notification.item)
                            }
                            target.removeAttr("data-kt-indicator", "on");
                            if (!notification.has_more) {
                                target.remove()
                            }
                        }
                    },
                });
            }
        })
    })
</script> 

