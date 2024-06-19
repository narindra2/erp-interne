<x-base-layout>
    <div class="card mb-5 ">
        <div class="card-body ">
            <div class="d-flex overflow-auto ">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent  fw-bolder flex-nowrap">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 "  id="info-tab" href="#info" data-toggle="ajax-tab" data-bs-toggle="tab"  data-load-url = "{{ url("/item-movements/assign") }}">
                           Inventaire
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 " href="#article" data-toggle="ajax-tab"   data-bs-toggle="tab" data-load-url = "{{ url("/stock/article/tab") }}">
                           Arlicles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 " href="#categorie" data-toggle="ajax-tab" data-bs-toggle="tab" data-load-url = "{{ url("/stock/category/tab") }}">
                           Catégories
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row g-6 g-xl-9">
        <div class="tab-content" >
            <div class="tab-pane fade" id="info" role="tabpanel"></div>
            <div class="tab-pane fade" id="article" role="tabpanel"></div>
            <div class="tab-pane fade" id="categorie" role="tabpanel"></div>
        </div>
    </div>

</x-base-layout>
