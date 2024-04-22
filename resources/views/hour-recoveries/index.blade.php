<x-base-layout>
    <div class="row">
        <div class="col-xxl-12 mb-10" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-header border-1 pt-1">
                    <div class="me-2 card-title align-items-start flex-column">
                        <span class="card-label  fs-3 mb-1"> @lang('lang.hour_recovery_management') </span>
                        <div class="text-muted fs-7 fw-bold"></div>
                    </div>
                    <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover"
                        data-bs-original-title="{{ trans('lang.type_of') }}">
                        @php
                            echo modal_anchor(url("/hour-recoveries/form"), '<i class="fas fa-plus"></i>' . trans('lang.new_hour_recovery'), ['title' => trans('lang.hour_recovery'), 'class' => 'btn btn-sm btn-light-primary', 'data-modal-lg' => true]);
                        @endphp
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-12" id="category-section">
            <div class="card shadow-sm card-xxl-stretch mb-3 mb-xl-1">
                <div class="card-body py-5">
                    <table id="hourRecovery" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            $(document).ready(function() {
                dataTableInstance.hourRecovery = $("#hourRecovery").DataTable({
                    processing: true,
                    columns: [ 
                        {data :"date_of_absence" , title: "Date d'absence"},
                        {data :"fullname" , title: 'Nom'},
                        {data :"job" , title: 'Poste'},
                        {data :"recovery_date" , title: 'Date de récupération'},
                        {data :"duration_of_absence" , title: "Durée d'absence"},
                        {data :"hour_absence" , title: "Heure d'absence"},
                        {data: "description", title: 'Nature'},
                        {data: "is_validated", title: 'Etat'},
                        @if (auth()->user()->isAdmin() || auth()->user()->isHR() || auth()->user()->isCp() || in_array( auth()->user()->id, \App\Models\Menu::$USER_ALLOWED_PART_ACCESS["complement_hours"]) )
                            {data: "action", orderable: false, searchable: false}
                        @endif
                    ],
                    ajax: {
                        url: url("/hour-recoveries-dataList"),
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    },
                });
            })
        </script>
    @endsection
</x-base-layout>
