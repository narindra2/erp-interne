<x-base-layout>
        <div class="card-body bg-white shadow-sm ">
            <ul class="nav nav-stretch nav-line-tabs  border-transparent  fw-bolder flex-nowrap mx-4">
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-6 "   href="#inventor-stock" data-toggle="ajax-tab" data-bs-toggle="tab"  data-load-url = "{{ url("/stock/inventory/tab") }}">
                       Inventaire des articles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-6 " href="#article-list" data-toggle="ajax-tab"   data-bs-toggle="tab" data-load-url = "{{ url("/stock/article/tab") }}">
                       Articles existants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-6 " href="#categorie-list" data-toggle="ajax-tab" data-bs-toggle="tab" data-load-url = "{{ url("/stock/category/tab") }}">
                       Catégorie des articles
                    </a>
                </li>
            </ul>
            <div class="tab-content mt-2" >
                <div class="tab-pane fade" id="inventor-stock" role="tabpanel"></div>
                <div class="tab-pane fade" id="article-list" role="tabpanel"></div>
                <div class="tab-pane fade" id="categorie-list" role="tabpanel"></div>
            </div>
        </div>
        

</x-base-layout>
