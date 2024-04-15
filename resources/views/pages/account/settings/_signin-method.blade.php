<!--begin::Sign-in Method-->
<div class="card card-flush shadow-sm {{ $class ?? '' }}" {{ util()->putHtmlAttributes(array('id' => $id ?? '')) }}>
    <!--begin::Card header-->
    <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_signin_method">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">@lang("lang.Sign-in Method")</h3>
        </div>
    </div>
    <!--end::Card header-->

    <!--begin::Content-->
    <div id="kt_account_signin_method" class="collapse show">
        <!--begin::Card body-->
        <div class="card-body border-top p-9">
            <div class="form-group row py-4">
                <label  class="col-md-3 fw-bolder ">@lang('lang.identification')</label>
                <div class="col-md-6">
                    <input disabled class="form-control form-control-transparent" type="text" value="{{ $user->registration_number }}" placeholder="Votre numero matricule" />
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center">
                <!--begin::Label-->
                <div id="kt_signin_email">
                    <div class="fs-6 fw-bolder mb-1">@lang("lang.Email Address")</div>
                    <div class="fw-bold text-gray-600">{{ auth()->user()->email }}</div>
                </div>
                <!--end::Label-->

                <!--begin::Edit-->
                
                <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                    <!--begin::Form-->
                    <form id="kt_signin_change_email" class="form" novalidate="novalidate" method="POST" action="{{ route('settings.changeEmail') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="current_email" value="{{ auth()->user()->email }} "/>
                        
                        <div class="row mb-6">
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                <div class="fv-row mb-0">
                                    <label for="email" class="form-label fs-6 fw-bolder mb-3">@lang("lang.Enter New Email Address")</label>
                                    <input type="email" class="form-control form-control-lg form-control-solid" placeholder="@lang('lang.Email Address')" name="email" value="{{ old('email') }}" id="email"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="fv-row mb-0">
                                    <label for="current_password" class="form-label fs-6 fw-bolder mb-3">{{ trans('lang.Confirm Password') }}</label>
                                    <input type="password" class="form-control form-control-lg form-control-solid" name="current_password" id="current_password"/>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <button id="kt_signin_submit" type="button" class="btn btn-primary  me-2 px-6">
                                @include('partials.general._button-indicator', ['label' => trans('lang.Update Email')])
                            </button>
                            <button id="kt_signin_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">{{ trans('lang.Cancel') }}</button>
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Edit-->

                <!--begin::Action-->
                <div id="kt_signin_email_button" class="ms-auto">
                    <button class="btn btn-light btn-active-light-primary">@lang("lang.Change Email")</button>
                </div>
                <!--end::Action-->
            </div>
            <!--end::Email Address-->

            <!--begin::Separator-->
            <div class="separator separator-dashed my-6"></div>
            <!--end::Separator-->

            <!--begin::Password-->
            <div class="d-flex flex-wrap align-items-center mb-10">
                <!--begin::Label-->
                <div id="kt_signin_password">
                    <div class="fs-6 fw-bolder mb-1">@lang("lang.Password")</div> 
                    <div class="fw-bold text-gray-600">************</div>
                </div>
                <!--end::Label-->

                <!--begin::Edit-->
                <div id="kt_signin_password_edit" class="flex-row-fluid d-none">
                    <!--begin::Form-->
                    <form id="kt_signin_change_password" class="form" novalidate="novalidate" method="POST" action="{{ route('settings.changePassword') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="current_email" value="{{ auth()->user()->email }} "/>
                        <div class="row mb-1">
                            <div class="col-lg-4">
                                <div class="fv-row mb-0">
                                    <label for="current_password" class="form-label fs-6 fw-bolder mb-3">{{ trans('lang.Current Password') }}</label>
                                    <input type="password" class="form-control form-control-lg form-control-solid" name="current_password" id="current_password"/>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="fv-row mb-0">
                                    <label for="password" class="form-label fs-6 fw-bolder mb-3">{{ trans('lang.New Password') }}</label>
                                    <input type="password" class="form-control form-control-lg form-control-solid" name="password" id="password"/>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="fv-row mb-0">
                                    <label for="password_confirmation" class="form-label fs-6 fw-bolder mb-3">{{ trans('lang.Confirm New Password') }}</label>
                                    <input type="password" class="form-control form-control-lg form-control-solid" name="password_confirmation" id="password_confirmation"/>
                                </div>
                            </div>
                        </div>

                        <div class="form-text mb-5">@lang("lang.Password must be at least 8 character and contain symbols")</div>

                        <div class="d-flex">
                            <button id="kt_password_submit" type="button" class="btn btn-primary me-2 px-6">
                                @include('partials.general._button-indicator', ['label' => trans('lang.Update Password')])
                            </button>
                            <button id="kt_password_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">{{ trans('lang.Cancel') }}</button>
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Edit-->

                <!--begin::Action-->
                <div id="kt_signin_password_button" class="ms-auto">
                    <button class="btn btn-light btn-active-light-primary">@lang("lang.Reset Password")</button>
                </div>
                <!--end::Action-->
            </div>
            <!--end::Password-->

        </div>
        <!--end::Card body-->
    </div>
</div>
<script src="{{asset("/demo1/js/custom/account/settings/signin-methods.js")}}"></script>
