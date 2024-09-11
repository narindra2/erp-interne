<x-base-layout>
    <div class="card shadow-sm mb-3">
        <div class="card-header">
            <h5 class="card-title"> @lang('lang.meeting_room')</h5>
            <div class="card-toolbar">
            </div>
        </div>
    </div>
    <div class="row">
        @foreach ($rooms as $room)
            <div class="col-md-4">
                <div class="card h-md-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">{{ $room->name }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-light-info " id="collapse-{{ $room->id }}" type="button" data-bs-toggle="collapse"
                                data-bs-target="#kt_accordion_{{ $room->id }}_body_{{ $room->id }}"
                                aria-controls="kt_accordion_{{ $room->id }}_body_{{ $room->id }}">
                                Utiliser la salle &nbsp;
                            </button>
                            
                        </div>
                    </div>
                    <div class="card-body ">
                        <form class="form" id="meeting-form-{{ $room->id }}" method="POST"
                            action="{{ url('/meeting-room/store-meeting') }}">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            <div class="accordion" id="kt_accordion_{{ $room->id }}">
                                <div class="row">
                                    <div class="form-group col-md-10">
                                        <label for="day_meeting" class="form-label">@lang('lang.date_meeting') : </label>
                                        <div class="">
                                            <input type="text"
                                                class="form-control form-control-solid day-meeting-{{ $room->id }}"
                                                autocomplete="off" placeholder="Date du réunion"
                                                value="{{ $now }}" name="day_meeting"
                                                id="day_meeting_{{ $room->id }}" />
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 mt-10">
                                        @php
                                            echo modal_anchor(url('/meeting-room/load/calendar'), '<i class="far fa-calendar-alt"></i>' , [
                                                'title' => "Calendrier du $room->name",
                                                'data-modal-lg' => true,
                                                'data-post-room_id' => $room->id ,
                                                'class' => 'btn btn-sm btn-light-info ',
                                            ]);
                                        @endphp
                                    </div>
                                </div>
                                <div id="kt_accordion_{{ $room->id }}_body_{{ $room->id }}"
                                    class="accordion-collapse collapse "
                                    aria-labelledby="kt_accordion_{{ $room->id }}_header_{{ $room->id }}"
                                    data-bs-parent="#kt_accordion_{{ $room->id }}">
                                    <div class="">
                                        <div class="form-group mb-3">
                                            <label for="horaires" class="form-label ">Heures : </label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-solid"
                                                        autocomplete="off"placeholder="Début ..." value=""
                                                        name="time_start" id="time_start_{{ $room->id }}" />
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-solid"
                                                        autocomplete="off" placeholder="Fin ..."value=""
                                                        name="time_end" id="time_end_{{ $room->id }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="type" class="form-label require">@lang('lang.type') :
                                            </label>
                                            <select name="type" class="form-select form-select-solid form-control-lg"
                                                data-control="select2" data-hide-search="true">
                                                <option selected value="Ne pas definis ">Ne pas definis </option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type }}">{{ $type }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <div>
                                                <label for="subject" class="form-label require">@lang('lang.subject') :
                                                </label>
                                                <div class="input-group ">
                                                    <span class="input-group-text">
                                                        <span class="svg-icon svg-icon-2x">
                                                            <i class="fas fa-list"></i>
                                                        </span>
                                                    </span>
                                                    <input type="text" id="subject-{{ $room->id  }}"
                                                        class="form-control form-control-solid" value=""
                                                        autocomplete="off" name="subject"
                                                        placeholder="Sujet de la reunion (Facultatif)" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" id="submit-{{ $room->id }}"
                                                class=" btn btn-sm btn-light-info mr-2">
                                                @include('partials.general._button-indicator', [
                                                    'label' => 'Créer cette reunion',
                                                    'message' => trans('lang.sending'),
                                                ])
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        {{-- <ul class="nav nav-stretch nav-pills nav-pills-custom nav-pills-active-custom d-flex justify-content-between mb-8 px-5" role="tablist">
                        @foreach ($day_in_week as $day)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link  d-flex flex-column  min-w-40px btn btn-sm btn-active-primary day-in-week-{{ $room->id }} " data-date="{{ $day["date"] }}"  data-is-past="{{ $day["is_past"] }}" title="{{ $day["date"] }}"  data-bs-toggle="tab" href="#" aria-selected="false" tabindex="-1" role="tab">
                                    <span class="fs-7 fw-semibold">{{ $day["date_string"] }} </span>
                                </a>
                            </li>
                        @endforeach
                    </ul> --}}
                        <div class="form-group mt-3">
                            <div id="horaires-list-{{ $room->id }}" role="tabpanel">
                                <div class="d-flex justify-content-center">
                                    <div class="spinner-border text-primary " style="width: 2rem; height: 2rem;"
                                        role="status">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @section('dynamic_link')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endsection
    @section('dynamic_script')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @endsection
    @section('scripts')
        <script>
            $(document).ready(function() {
                var loadingH = `<div class="d-flex justify-content-center">
                                            <div class="spinner-border text-primary " style="width: 2rem; height: 2rem;" role="status">
                                            </div>
                                        </div>`;
                @foreach ($rooms as $room)
                    var looping_{{ $room->id }} = null;
                    $(".day-meeting-{{ $room->id }}").on("change", function() {
                        $("#horaires-list-{{ $room->id }}").html(loadingH);
                        getHorairesData({
                            "day_meeting": $(this).val(),
                            "room_id": "{{ $room->id }}",
                            "_token": _token,
                            "target": "#horaires-list-{{ $room->id }}"
                        });
                    });
                    looping_{{ $room->id }} = setInterval(() => {
                        let day = $("#day_meeting_{{ $room->id }}").val();
                        if (day) {
                            getHorairesData({
                                "day_meeting": day,
                                "room_id": "{{ $room->id }}",
                                "_token": _token,
                                "target": "#horaires-list-{{ $room->id }}"
                            })
                        }
                    }, 3000);
                    // $("#day_meeting_{{ $room->id }}").daterangepicker({
                    //     singleDatePicker: true,
                    //     showDropdowns: true,
                    //     autoApply: true,
                    //     autoUpdateInput: false,
                    //     minDate: "{{ $now }}",
                    //     locale: {
                    //         format: 'DD/MM/yyyy',
                    //     }
                    // }).on('apply.daterangepicker', function(ev, picker) {
                    //     $(this).val(picker.startDate.format('DD/MM/yyyy'))
                    //     $(this).change()
                    // }).on('cancel.daterangepicker', function(ev, picker) {
                    //     $(this).val("")
                    //     $(this).change()
                    // });
                    $("#day_meeting_{{ $room->id }}").flatpickr({
                        minDate: "today",
                        dateFormat: "d/m/Y",
                        mode: "multiple",
                        defaultDate: ["{{ $now }}"]
                    }
                    );
                    $("#time_start_{{ $room->id }} ,#time_end_{{ $room->id }}").flatpickr({
                        enableTime: true,
                        noCalendar: true,
                        time_24hr: true,
                        dateFormat: "H:i",
                    });
                    $("#meeting-form-{{ $room->id }}").appForm({
                        isModal: false,
                        submitBtn: "#submit-{{ $room->id }}",
                        onSuccess: function(response) {
                            $("#collapse-{{ $room->id }}").trigger("click");
                            $("#time_start_{{ $room->id }} ,#time_end_{{ $room->id }}, #subject-{{ $room->id }}").val("");
                        },
                    })
                    function getHorairesData(params = {}) {
                        $.ajax({
                            type: 'post',
                            url: url("/meeting-room/load/horaires"),
                            dataType: 'html',
                            data: params,
                            success: function(response) {
                                $(params.target).html(response);
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                if (xhr.status == 419) {
                                    clearInterval(looping_{{ $room->id }});
                                }
                            }
                        });
                    }
                @endforeach
            })
        </script>
    @endsection
</x-base-layout>
