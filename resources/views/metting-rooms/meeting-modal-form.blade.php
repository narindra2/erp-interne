<form class="form" id="meeting-modal-form" method="POST" action="{{ $horaire ? url('/meeting-room/update-meeting') : url('/meeting-room/store-meeting') }}">
    <div class="card-body">
        <div class="card card-flush shadow-sm ">
            <div class="card-body">
                @csrf
                <input type="hidden" name="room_id" value="{{ $horaire->room_id ?? $room->id   }}">
                <input type="hidden" name="horaire_id" value="{{ $horaire->id ?? 0  }}">
                <div class="form-group">
                    <label for="horaires" class="form-label require">Salle réuion: </label>
                        <div class="mb-3  col-md-6">
                            <input type="text"  disabled = "true" class="form-control form-control-solid" value="{{ $room->name }}"/>
                        </div>
                </div>
                <div class="form-group">
                <label for="type" class="form-label require">@lang('lang.type') : </label>
                <select name="type" class="form-select form-select-solid form-control-lg"
                            data-dropdown-parent="#ajax-modal" data-control="select2" data-hide-search="true">
                            <option  selected value="Ne pas definis " >Ne pas definis </option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" @if ($horaire && $horaire->type == $type) selected @endif>{{ $type }} </option>
                            @endforeach
                        </select>
                </div>
                <div class="form-group">
                    <div class="mb-3">
                        <label for="subject" class="form-label require">@lang('lang.subject') : </label>
                        <div class="input-group mb-5">
                            <span class="input-group-text" >
                                <span class="svg-icon svg-icon-2x">
                                    <i class="fas fa-list"></i>
                                </span>
                            </span>
                            <input type="text" id="subject" class="form-control form-control-solid" value="{{ $horaire ? $horaire->subject : "" }}" autocomplete="off"   name="subject" placeholder="Sujet de la reunion (Facultatif)" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="day_meeting" class="form-label require">@lang('lang.day_meeting') : </label>
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-solid" autocomplete="off"  placeholder="Date du réunion" value="{{ $horaire ? convert_database_date($horaire->day_meeting) : "" }}"  name="day_meeting" id="day_meeting" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="horaires" class="form-label require">Horaires: </label>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <input type="text" class="form-control form-control-solid" autocomplete="off"placeholder="Début ..." value="{{ $horaire ? $horaire->start : ""  }}"  name="time_start" id="time_start" />
                        </div>
                        <div class="mb-3  col-md-6">
                            <input type="text" class="form-control form-control-solid" autocomplete="off" placeholder="Fin ..."value="{{ $horaire ? $horaire->end : ""  }}" name="time_end" id="time_end" />
                        </div>
                    </div>
                </div>
                @if ($horaire)
                    <div class="form-group">
                        <label for="horaires" class="form-label require">Suppression : </label>
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="1" name="deleted" id="deleted"/>
                            <label class="form-check-label" for="deleted">
                                Supprimer cet occupation du salle ?
                            </label>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-dark btn-sm mr-2 ">
            @lang('lang.cancel')
        </button>
        &nbsp;
        <button type="submit" id="submit"class=" btn btn-sm btn-light-success  mr-2">
            @include('partials.general._button-indicator', [
                'label' => $horaire ? "Mettre à jour" : "Créer",
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#day_meeting").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoApply: true,
            autoUpdateInput: false,
            minDate: "{{ $now }}",
            locale: {
                format: 'DD/MM/yyyy',
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/yyyy'))
            $(this).change()
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val("")
            $(this).change()
        });

        $("#time_start ,#time_end").flatpickr({
            enableTime: true,
            noCalendar: true,
            time_24hr: true,
            dateFormat: "H:i",
        });
        $("#meeting-modal-form").appForm({
            onSuccess: function(response) {
                
            },
        })
    });
</script>
