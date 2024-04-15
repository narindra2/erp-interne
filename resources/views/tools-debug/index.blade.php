<x-base-layout>
    <div class="card shadow-sm  ">
        <div class="card-header border-1 pt-1">
            <div class="me-2 card-title align-items-start ">
                <span class="card-label  fs-3 mb-1"> @lang('lang.debug') </span>
                <div class="text-muted fs-7 fw-bold"></div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm  ">
        <div class="card-body py-5">
            <div class="d-flex flex-column flex-md-row rounded border p-10">
            <ul class="nav nav-tabs nav-pills flex-row border-0 flex-md-column me-5 mb-3 mb-md-0 fs-6 min-w-lg-200px">
                <li class="nav-item w-md-200px me-0">
                    <a class="nav-link active" data-bs-toggle="tab" href="#account"> 
                        <span class="d-flex flex-column align-items-start">
                        <span class="fs-4 fw-bold">Comptes</span>
                        <span class="fs-7">Re-inite. mdp, Bloquage, ...</span>
                    </span></a>
                </li>
              
                <li class="nav-item w-md-200px">
                    <a class="nav-link" data-bs-toggle="tab" href="#settings"> 
                        <span class="d-flex flex-column align-items-start">
                            <span class="fs-4 fw-bold">ERP</span>
                            <span class="fs-7">Parametres, Laravel cmd, ...</span>
                        </span>
                    </a>
                </li>
            </ul>

            <div class="tab-content" >
                <div class="tab-pane fade show active" id="account" role="tabpanel">
                    <div class="card shadow-lg card-docs flex-row-fluid col-md-12">
                        <div class="card-header">
                            <h3 class="card-title">Réinitialisation de  << mot de passe >> </h3>
                        </div>
                        <form class="form" id="reset-pwd" method="POST" action="{{ url('/outils-debug/reset-pwd') }}">
                        <div class="card-body">
                            <div class="row">
                                @csrf
                                <div class="mb-3 col-md-12">
                                    <label for="users"> Réinitialiser le mot depas ERP de :</label>
                                    <select class="form-select" name="users[]" id="users" data-control="select2" data-close-on-select="false" data-placeholder="Selectionner les utilisatuers" data-allow-clear="true" multiple="multiple">
                                        <option value="0" disabled  >--Collaborateurs--</option>
                                            @foreach ($users as $user)
                                                <option data-avatar= "{{  $user->avatarUrl }}" value="{{ $user->id }}">{{ $user->sortname}} ({{ $user->registration_number }}) </option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="users">Réinitialiser les mot de passe en :</label>
                                    <input type="text" class="form-control" name="new_pwd" id="new_pwd" placeholder="{{ $pwd_default }}" value="{{ $pwd_default }}"/>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" id="submit-1" class=" btn btn-sm btn-light-danger  mr-2">
                                    @include('partials.general._button-indicator', ['label' => "Reinitiliser ?","message" =>
                                    trans('lang.sending')])
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="card shadow-lg card-docs flex-row-fluid col-md-12">
                        <div class="card-header">
                            <h3 class="card-title">Réinitialisation de &nbsp;<u> Toutes </u>   &nbsp;les << mot de passe >> </h3>
                        </div>
                        <form class="form" id="reset-pwd-all" method="POST" action="{{ url('/outils-debug/reset-pwd-all') }}">
                        <div class="card-body">
                            <div class="row">
                                @csrf
                                <div class="mb-3 col-md-12">
                                    <label for="users-exluded" > Réinitialiser toutes les mot de passe ERP <strong style="color: red"> sauf </strong>  :</label>
                                    <select class="form-select" name="users[]" id="users-exluded" data-control="select2" data-close-on-select="false" data-placeholder="Selectionner les utilisatuers" data-allow-clear="true" multiple="multiple">
                                        <option value="0" disabled  >--Collaborateurs--</option>
                                        @php
                                            $userType = App\Models\UserType::class;
                                        @endphp
                                            @foreach ($users as $user)
                                                <option 
                                                @if (in_array($user->user_type_id,[$userType::$_ADMIN,$userType::$_HR,$userType::$_TECH]))
                                                    selected
                                                @endif
                                                data-avatar= "{{  $user->avatarUrl }}" 
                                                value="{{ $user->id }}">{{ $user->sortname}} ({{ $user->registration_number }}) 
                                            </option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="users">Réinitialiser les mot de passe en :</label>
                                    <input type="text" class="form-control" name="new_pwd" id="new_pwd" placeholder="{{ $pwd_default }}" value="{{ $pwd_default }}"/>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" id="submit-2" class=" btn btn-sm btn-light-danger  mr-2">
                                    @include('partials.general._button-indicator', ['label' => "Réinitialiser ?","message" =>
                                    trans('lang.sending')])
                                </button>
                            </div>
                        </div>
                    </form>
                    </div>
               
                </div>
                <div class="tab-pane fade" id="settings" role="tabpanel">
                    En cours de devellopement !
                </div>
            </div>
        </div>
    </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                $("#reset-pwd").appForm({
                    isModal:false,
                    submitBtn : "#submit-1",
                    onSuccess: function(response) {
                        $("#users").val("");
                    },
                })
                $("#reset-pwd-all").appForm({
                    isModal:false,
                    submitBtn : "#submit-2",
                    onSuccess: function(response) {
                        
                    },
                })
                        
                var optionFormat = function(item) {
                if ( !item.id ) {
                    return item.text;
                }
                var span = document.createElement('span');
                var imgUrl = item.element.getAttribute('data-avatar');
                var template = '';
                if (imgUrl) {
                    template += '<img src="' + imgUrl + '" class="rounded-circle h-20px me-2" alt="image"/>';
                }
                template += "     " +  item.text;
                span.innerHTML = template;
                return $(span);
            }

            $('#users').select2({
                templateSelection: optionFormat,
                templateResult: optionFormat
            });
            $('#users-exluded').select2({
                templateSelection: optionFormat,
                templateResult: optionFormat
            });
               
            })
        </script>
    @endsection
</x-base-layout>
