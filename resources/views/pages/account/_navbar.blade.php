{{-- @php
    $nav = array(
        array('title' => trans("lang.my_info"), 'view' => 'settings/info'),
        array('title' => trans("lang.dayoff"), 'view' => 'my-days-off'),
        array('title' => trans("lang.Settings"), 'view' => 'account/settings'),
    );
@endphp --}}

<div class="card {{ $class }}">
    <div class="card-body pt-9 pb-0">
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
            <div class="me-7 mb-4">
                <form id="update-avatar-file" method="POST" action="{{ url("/user/update/avatar") }}">
                <div class="symbol symbol-200px symbol-lg-160px symbol-fixed position-relative">
                    <div id="avatar-user" class="image-input image-input-circle" data-kt-image-input="true" style="background-image: url(/assets/media/svg/avatars/blank.svg)">
                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ auth()->user()->getAvatarFormat() }})"></div>
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                           data-kt-image-input-action="change"
                           data-bs-toggle="tooltip"
                           data-bs-dismiss="click"
                           title="Change avatar">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            @csrf
                            <input type="file"  name="avatar" accept=".png, .jpg, .jpeg" />
                            <input type="hidden" name="avatar_remove" value="0" />
                        </label>
                        {{-- <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                           data-kt-image-input-action="cancel"
                           data-bs-toggle="tooltip"
                           data-bs-dismiss="click"
                           title="Cancel avatar">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                           data-kt-image-input-action="remove"
                           data-bs-toggle="tooltip"
                           data-bs-dismiss="click"
                           title="Remove avatar">
                            <i class="bi bi-x fs-2"></i>
                        </span> --}}
                    </div>
                </div>
                <button type="submit" hidden id="submit-user-avatar" ></button>
            </form>
            </div>
            <div class="flex-grow-1">
                <!--begin::Title-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::User-->
                    <div class="d-flex flex-column">
                        <!--begin::Name-->
                        <div class="d-flex align-items-center mb-2">
                            <a href="#" id="typedjs_user" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1"></a>
                            <script src=" {{ asset('demo1/plugins/custom/typedjs/typedjs.bundle.js') }}"></script>
                            @php
                                $motivations = [
                                    "Tu est top . 🎉😎",
                                    "Tu est  génial , Force à toi  🥳.",
                                    "Le succès n'est pas final, l'échec n'est pas fatal.",
                                    "Croyez en vos rêves et ils se réaliseront peut-être.",
                                    " Tu ne perds jamais. Soit  tu gagnes, soit tu t'apprends.",
                                    " La chance : plus vous la travaillez, plus elle vous sourit.",
                                    " Le meilleur moyen de prévoir le futur, c’est de le créer.",
                                    "Le futur appartient à ceux qui croient à la beauté de leurs rêves.",
                                    "L’action est la clé fondamentale de tout succès.",
                                    "Les gagnants trouvent des moyens, les perdants des excuse...",
                                    "Soyez le changement que vous voulez voir dans le monde.",
                                    "Votre avenir est créé par ce que vous faites aujourd’hui, pas demain.",
                                    "Les erreurs sont les portes de la découverte.",
                                    "Le plus grand de tous les risques est de ne pas en prendre.",
                            ];
                            @endphp
                            <script>
                                var typed = new Typed("#typedjs_user", {
                                    strings: ["Bonjour  {{ $user->sortname }} 👋!"  ,"... ...","«{{ $motivations[rand(0,count( $motivations) - 1 )] }}»"  ,"😊🥳","{{ $user->fullname }}"],
                                    typeSpeed: 30
                                })
                            </script>
                            <a href="#">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen026.svg", "svg-icon-1 svg-icon-primary") !!}
                            </a>
                        </div>
                        <!--end::Name-->

                        <!--begin::Info-->
                        <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-4 me-1") !!}
                                {{ $user->actual_job }}
                            </a>
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen018.svg", "svg-icon-4 me-1") !!}
                                {{ $user->address }}
                            </a>
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/communication/com011.svg", "svg-icon-4 me-1") !!}
                                {{ $user->email }}
                            </a>
                        </div>
                        <!--end::Info-->
                    </div>
                    <div class="d-flex my-4">
                        @php
                            echo modal_anchor(url("/request_days_off/modal"), "+ Demande de congés", ["title" => "Formulaire de la demande", "data-modal-lg" => true, "class"=> "btn btn-sm btn-light-primary", "data-post-id" => 1]);
                        @endphp
                        {{-- <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-trigger="hover" title="Coming soon">Hire Me</a>
                        <div class="me-0">
                            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="bi bi-three-dots fs-3"></i>
                            </button>
                            {{ theme()->getView('partials/menus/_menu-3') }}
                        </div> --}}
                    </div>
                </div>

                <div class="d-flex flex-wrap flex-stack">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap">
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                {{-- <div class="d-flex align-items-center"> --}}
                                    {{-- {!! theme()->getSvgIcon("icons/duotune/arrows/arr066.svg", "svg-icon-3 svg-icon-success me-2") !!} --}}
                                    <div class="fs-2 fw-bolder">{{ $minute_worked }}</div> 
                                {{-- </div> --}}
                                <!--end::Number-->

                                <!--begin::Label-->
                                <div class="fw-bold fs-6 text-gray-400">{{ trans('lang.Pointing') }}</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    {{-- {!! theme()->getSvgIcon("icons/duotune/arrows/arr065.svg", "svg-icon-3 svg-icon-danger me-2") !!} --}}
                                    <div class="fs-2 fw-bolder">{{ $user->nb_days_off_remaining }}</div>
                                    
                                </div>
                                <!--end::Number-->

                                <!--begin::Label-->
                                <div class="fw-bold fs-6 text-gray-400">{{ trans('lang.Leave Balance') }}</div>
                                <!--end::Label-->
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    @php
                                        $max_permission = App\Models\DayOff::$_max_permission_on_year;
                                        $permission_total =App\Models\User::get_cache_total_permission($user->id);
                                    @endphp
                                    <div class="fs-2 fw-bolder">{{ $max_permission - $permission_total }} / {{ $max_permission ." jr(s)" }}</div>
                                </div>
                                <!--end::Number-->

                                <!--begin::Label-->
                                <div class="fw-bold fs-6 text-gray-400">Permission </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                        </div>
                        <!--end::Stats-->
                    </div>

                    {{-- <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-bold fs-6 text-gray-400">{{ __('Profile Completion') }}</span>
                            <span class="fw-bolder fs-6">50%</span>
                        </div>

                        <div class="h-5px mx-3 w-100 bg-light mb-3">
                            <div class="bg-success rounded h-5px" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="d-flex overflow-auto h-55px">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
                   @if (!auth()->user()->isRhOrAdmin())
                   <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 "  id="info-tab" href="#info" data-toggle="ajax-tab" data-bs-toggle="tab"  data-load-url = "{{ url("/user/tab/report") }}">
                            Rapport
                        </a>
                    </li>
                   @endif
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 "  id="info-tab" href="#info" data-toggle="ajax-tab" data-bs-toggle="tab"  data-load-url = "{{ url("/user/tab/info") }}">
                           Mes infos 
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 " href="#my_dayoff" data-toggle="ajax-tab"   data-bs-toggle="tab" data-load-url = "{{ url("/user/tab/work-info") }}">
                           {{  trans("lang.info_work") }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 " href="#account-signin" data-toggle="ajax-tab"  data-bs-toggle="tab" data-load-url = "{{ url("/user/tab/connexion") }}">
                          {{ trans("lang.authentication") }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 " href="#sanction" data-toggle="ajax-tab"  data-bs-toggle="tab" data-load-url = "{{ url("/user/sanction") }}">
                          {{ trans("lang.sanction") }}
                        </a>
                    </li>
                </ul>
        </div>
    </div>
</div>
@section('scripts')
<script>
    $(document).ready(function(){
        $("#update-avatar-file").appForm({
            onSuccess: function(response) {
            },
        })
        var imageInputElement = document.querySelector("#avatar-user");
        var imageInput = KTImageInput.getInstance(imageInputElement);
        imageInput.on("kt.imageinput.change", function() {
           $("#submit-user-avatar").trigger("click")
        });
    })
</script>   
@endsection
