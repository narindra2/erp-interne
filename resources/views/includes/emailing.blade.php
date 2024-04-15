<div class="container-fluid d-flex justify-content-end dropbottom" >
    <div class="dropbottom" id="mail">
        <span  id="mail_to" href="#"  aria-haspopup="true" data-bs-toggle="dropdown" aria-expanded="false">
        </span>
        <div class="dropdown-menu dropdown-menu-lg w-400px" id="data_mail" data-kt-menu="true">
            <form action="{{ url("send/email")}}" id="send-mail-form" method="POST">
                <div class="card">
                    <div class="card-body" style="margin-bottom: -50px;">
                        <div class="my-8">
                            @csrf
                            <label for="first_name" class=" form-label">Envoyé E-mail</label>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" class="form-control form-control-solid" id="mail_value"  data-rule-required="true" data-msg-required="@lang('lang.required_input')" data-rule-email="true" data-msg-email="@lang('lang.required_input_type_email')"name="to" autocomplete="off" placeholder="exemple@gmail.com"  />
                            </div>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" class="form-control form-control-solid" autocomplete="off" id="email_object" name="object" placeholder="Objet"/>
                            </div>
                            <div class="input-group mb-3">
                                <textarea class="form-control form-control-solid" data-rule-required="true" data-msg-required="@lang('lang.required_input')"  id="email_content" data-kt-autosize="true" rows="3" placeholder="message ..."></textarea>
                            </div>
                            <div class="align-items-center me-2">
                                <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="" data-bs-original-title="Fichier joint">
                                    <i class="bi bi-paperclip fs-3"></i>
                                </button>
                                {{-- <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="" data-bs-original-title="Coming soon">
                                    <i class="bi bi-upload fs-3"></i>
                                </button> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end " style=" padding: 1rem 2.25rem;background-color: transparent;">
                        <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
                            @include('partials.general._button-indicator', ['label' => trans('lang.send'),"message" =>trans("lang.sending")])
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
