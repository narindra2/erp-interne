<a href="#" class="btn btn-sm btn-light-primary ps-7 "  data-kt-menu-trigger="click"  data-kt-menu-placement="bottom-end">Gerér 
    {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg") !!}
    </a>
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold py-4 w-250px fs-6 " data-kt-menu="true" style="z-index: 105; position: fixed; inset: 0px 0px auto auto; margin: 0px; transform: translate(-162px, 196px);" data-popper-placement="bottom-end">
        <div class="menu-item px-5">
            <div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">Ajouter/Editer </div>
        </div>
        <div class="separator my-3"></div>
        <div class="menu-item px-5">
            @php
                echo  modal_anchor(url("/suivi/custom-filter-modal"), trans('lang.add_filter') , ['title' => "Créer mon filtre personnalisé" , "class" => "menu-link px-5", "data-modal-lg" => true]);
            @endphp
        </div>
        <div class="menu-item px-5">
            @php
                echo  modal_anchor(url("/suivi/version-modal"), trans('lang.add_version') , ['title' => "Ajouter un nouveau version" , "class" => "menu-link px-5", "data-modal-lg" => true]);
            @endphp
        </div>
        {{-- <div class="menu-item px-5">
            @php
                echo  modal_anchor(url("/suivi/level-modal"), trans('lang.level') , ['title' => "Gerer les niveaux" , "class" => "menu-link px-5", "data-modal-lg" => true]);
            @endphp
        </div> --}}
        <div class="menu-item px-5">
            @php
                echo  modal_anchor(url("/suivi/point-modal"), trans('lang.clientType_projectType_level_point_pointSupp') , ['title' => "Gerer les points" , "class" => "menu-link px-5", "data-modal-lg" => true]);
            @endphp
        </div>
        <div class="menu-item px-5">
            @php
                echo  modal_anchor(url("/suivi/type-client"), trans('lang.client_types') , ['title' => "Ajouter un nouveau type de client" , "class" => "menu-link px-5", "data-modal-lg" => true]);
            @endphp
        </div>
        <div class="menu-item px-5">
            @php
                echo  modal_anchor(url("/suivi/type-modal"), trans('lang.add_type_project') , ['title' => "Ajouter un nouveau type projet" , "class" => "menu-link px-5", "data-modal-lg" => true]);
            @endphp
        </div>
        
    </div>