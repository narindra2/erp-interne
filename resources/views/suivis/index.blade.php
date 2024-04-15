<x-base-layout>
    <div class="card">
        <div class="card-header " style="min-height: 46px;">
            <h5 class="card-title"> <span class="card-label  fs-3 "> @lang('lang.suivi')s  </span></h5>
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                    @if ($auth->isM2pOrAdmin() || $auth->isCp() )
                    {{-- <li class="nav-item">
                        <li class="nav-item">
                            <a href="{{ url("/suivi/v2/projet?tab=userparams&vue=true") }}" class="nav-link text-active-primary {{ $view =="userparams" ? "active" :"" }}" >
                                Params 
                            </a>
                        </li>
                    </li> --}}
                        <li class="nav-item">
                            <li class="nav-item">
                                <a href="{{ url("/suivi/v2/projet?tab=folderlist&vue=true") }}" class="nav-link text-active-primary {{ $view =="folderlist" ? "active" :"" }}" >
                                    Dossiers 
                                </a>
                            </li>
                        </li>
                    @endif
                    <li class="nav-item">
                        <li class="nav-item">
                            <a href="{{ url("/suivi/v2/projet?tab=table") }}" class="nav-link text-active-primary {{ $view =="table" ? "active" :"" }}" >
                                Traitement de dossiers 
                            </a>
                        </li>
                    </li>
                    <li class="nav-item">
                        <li class="nav-item">
                            <a href="{{ url("/suivi/v2/projet?tab=recapitulatif&vue=true") }}" class="nav-link text-active-primary {{ $view =="recapitulatif" ? "active" :"" }}" >
                                Point de productions
                            </a>
                        </li>
                    </li>
                    <li class="nav-item">
                        <li class="nav-item">
                            <a  href="{{ url("/suivi/v2/projet?tab=productivitie&vue=true") }}" class="nav-link text-active-primary {{ $view =="productivitie2" ? "active" :"" }}"   id="productivities-tab" >
                                 Productivités
                            </a>
                        </li>
                    </li>
                   
                    <li class="nav-item">
                        <a href="{{ url("/suivi/v2/projet?tab=statistique") }}" class="nav-link text-active-primary {{ $view =="statistique" ? "active" :"" }}">
                            Statistiques
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        {{-- <div class="card-body"> --}}
            <div class="tab-content">
                @if (isset($view))
                    @include("suivis.tabs.$view")
                @endif
            </div>
        {{-- </div> --}}
    </div>
</x-base-layout>
