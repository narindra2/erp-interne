<x-base-layout>
    {{ theme()->getView('pages/account/_navbar', array('class' => 'mb-5 mb-xl-10', 'user' => $user, 'minute_worked' => $minute_worked)) }}
    <div class="row g-6 g-xl-9">
        <div class="tab-content" id="tab-user-info">
            <div class="tab-pane fade " id="descriptions" role="tabpanel"></div>
            <div class="tab-pane fade" id="info" role="tabpanel"></div>
            <div class="tab-pane fade" id="my_dayoff" role="tabpanel"></div>
            <div class="tab-pane fade" id="sanction" role="tabpanel"></div>
            <div class="tab-pane fade" id="account-signin" role="tabpanel"></div>
        </div>
    </div>
</x-base-layout>
