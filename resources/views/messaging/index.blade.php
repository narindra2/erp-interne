<x-base-layout>
    <div class="row">
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-xxl">
                    <div class="d-flex flex-column flex-lg-row">
                        
                        @include('messaging.contact-list', ['contacts' => $contacts, 'groups' => $groups, 'isAdmin' => $isAdmin])

                        <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
                            <div class="card" id="messageContent">
                                @include('messaging.welcome')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
