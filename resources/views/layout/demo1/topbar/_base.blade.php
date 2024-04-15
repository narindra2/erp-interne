@php
$toolbarButtonMarginClass = 'ms-1 ms-lg-3';
$toolbarButtonHeightClass = 'w-40px h-40px';
$toolbarUserAvatarHeightClass = 'symbol-40px';
$toolbarButtonIconSizeClass = 'svg-icon-1';
/** Notification Bell*/
$auth_user = auth()->user();
$take = App\Models\Notification::$_PER;
$notifications = App\Models\Notification::where('notifiable_id', $auth_user->id)
    ->latest()
    ->take($take)
    ->get();
$count_not_see = $notifications->whereNull('read_at')->count() ?? '';
@endphp

{{-- begin::Toolbar wrapper --}}
<div class="d-flex align-items-stretch flex-shrink-0">

    <div class="d-flex align-items-center">
        {{ theme()->getView('timer/index') }}
    </div>

    <div id="modalMessage" class="d-flex align-items-center {{ $toolbarButtonMarginClass }} position-relative me-5">
         <div class="btn btn-icon btn-active-light-primary {{ $toolbarButtonHeightClass }}" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3"
                        d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z"
                        fill="currentColor"></path>
                    <rect x="6" y="12" width="7" height="2" rx="1"
                        fill="currentColor"></rect>mess
                    <rect x="6" y="7" width="12" height="2" rx="1"
                        fill="currentColor"></rect>
                </svg>
                {{-- <span id="mess" class="position-absolute translate-middle badge badge-circle badge-light-danger mt-8">0</span> --}}
            </span>
        </div>
        {{ theme()->getView('partials/topbar/_messages') }}
    </div>
    <div id="modalMessageGroup" class="d-flex align-items-center {{ $toolbarButtonMarginClass }} position-relative me-5">
        <div class="btn btn-icon btn-active-light-primary {{ $toolbarButtonHeightClass }}" data-kt-menu-trigger="click"
            data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <span class="svg-icon svg-icon-1">
                <i class="fas fa-users fs-3"></i>
                {{-- <span id="mess" class="position-absolute translate-middle badge badge-circle badge-light-danger mt-8">0</span> --}}
            </span>
        </div>
        {{ theme()->getView('partials/topbar/_messages_groups') }}
    </div>

    <div class="d-flex align-items-center {{ $toolbarButtonMarginClass }}">
        <div id="bell-icon"
            class="btn btn-icon btn-active-light-primary position-relative pulse pulse-danger {{ $toolbarButtonHeightClass }}"
            data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end"
            data-kt-menu-flip="bottom">
            {!! theme()->getSvgIcon('icons/duotune/general/gen007.svg', $toolbarButtonIconSizeClass) !!}
            <span class="menu-badge position-absolute top-0 start-50 text-danger ">
                <span class="badge badge-light-danger badge-circle fw-bolder fs-7 ">
                    <span id="notifications-count">{{ $count_not_see }}</span>
                    <span class="pulse-notification" id="pulse-notification"></span>
                </span>
            </span>
        </div>
        {!! view('partials/topbar/_notifications-menu', ['take' => $take, 'notifications' => $notifications])->render() !!}
    </div>

    {{--begin::User--}}
    @if(Auth::check())
        <div class="d-flex align-items-center {{ $toolbarButtonMarginClass }}" id="kt_header_user_menu_toggle">
            {{-- begin::Menu --}}
            <div class="cursor-pointer symbol {{ $toolbarUserAvatarHeightClass }}" data-kt-menu-trigger="click"
                data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                <img src="{{ $auth_user->avatarUrl }}" alt="metronic" />
            </div>
            {{ theme()->getView('partials/topbar/_user-menu') }}
            {{-- end::Menu --}}
        </div>
    @endif
    {{-- end::User --}}

    {{-- begin::Heaeder menu toggle --}}
    @if (theme()->getOption('layout', 'header/left') === 'menu')
        <div class="d-flex align-items-center d-lg-none ms-2 me-n3" data-bs-toggle="tooltip"
            title="Show header menu">
            <div class="btn btn-icon btn-active-light-primary" id="kt_header_menu_mobile_toggle">
                {!! theme()->getSvgIcon('icons/duotune/text/txt001.svg', 'svg-icon-1') !!}
            </div>
        </div>
    @endif
    {{-- end::Header menu toggle --}}
</div>
{{-- end::Toolbar wrapper --}}