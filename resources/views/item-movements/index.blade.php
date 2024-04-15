<x-base-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-body pt-9 pb-0">
            <div class="d-flex overflow-auto h-55px">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 "  id="info-tab" href="#info" data-toggle="ajax-tab" data-bs-toggle="tab"  data-load-url = "{{ url("/item-movements/assign") }}">
                           Assignation
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 " href="#info" data-toggle="ajax-tab"   data-bs-toggle="tab" data-load-url = "{{ url("/item-movements/stock") }}">
                           En Stock
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 " href="#info" data-toggle="ajax-tab" data-bs-toggle="tab" data-load-url = "{{ url("/item-movements/items") }}">
                           Article
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row g-6 g-xl-9">
        <div class="tab-content" id="tab-user-info">
            <div class="tab-pane fade" id="info" role="tabpanel"></div>
        </div>
    </div>

</x-base-layout>
