<form action="{{ url("/days-off/giveResult") }}" method="POST" id="modal-form-info">
    <div class="card-body">
        @csrf
        <input type="hidden" name="id" value="{{ $dayOff->id }}" id="id">
        <div class="form-group row mb-4">
            <h3>Type d'absence: {{ $dayOff->type->getType() }}</h3>
        </div>

        @if ($dayOff->is_canceled)
            <p class="text-info text-center mb-4">La demande a été annulée</p>
        @endif
        <div class="form-group row mb-5">
            <div class="col-6 border-gray-300 border-end-dashed">
                <div class="form-group row">
                    <label class="col-form-label col-5">Date début</label>
                    <div class="col-6">
                        <input id="start_date" name="start_date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" value="{{ $dayOff->getStartDate()->format('d/m/Y') }}"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-5">Date de retour</label>
                    <div class="col-6">
                        <input id="return_date" name="return_date" data-rule-required="true" data-msg-required="@lang('lang.required_input')" class="form-control form-control-sm form-control-solid datepicker" autocomplete="off" name="start_date" placeholder="DD/MM/YYYY" data-rule-required="true" data-msg-required="@lang('lang.required_input')" value="{{ $dayOff->getReturnDate()->format('d/m/Y') }}"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-5">Durée (en jours)</label>
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm form-control-solid" value="{{ $dayOff->duration }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-5">Solde de congé restant (en jours)</label>
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm form-control-solid" value="{{ $dayOff->applicant->nb_days_off_remaining }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-5">Nature du congé</label>
                    <div class="col-6">
                        <select id="nature_id" name="nature_id" class="form-select form-select-sm form-select-solid" value="{{ $dayOff->nature->nature }} ">
                            <option value="1" @if ($dayOff && $dayOff->nature_id == 1) selected @endif>cours</option>
                            <option value="2" @if ($dayOff && $dayOff->nature_id == 2) selected @endif>En télétravail</option>
                            <option value="3" @if ($dayOff && $dayOff->nature_id == 3) selected @endif>En déplacement</option>
                            <option value="4" @if ($dayOff && $dayOff->nature_id == 4) selected @endif>En permission</option>
                            <option value="5" @if ($dayOff && $dayOff->nature_id == 5) selected @endif>Repos médical</option>
                            <option value="6" @if ($dayOff && $dayOff->nature_id == 6) selected @endif>En récupération</option>
                            <option value="7" @if ($dayOff && $dayOff->nature_id == 7) selected @endif>Absent(e)</option>
                            <option value="8" @if ($dayOff && $dayOff->nature_id == 8) selected @endif>En formation</option>
                            <option value="9" @if ($dayOff && $dayOff->nature_id == 9) selected @endif>En congé</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-5">Statut</label>
                    <div class="col-6">
                        <select id="result" name="result" class="form-select form-select-sm form-select-solid" value="{{ $dayOff->result }}" id="">
                            <option value="in_progress" @if ($dayOff->result == "in_progress") selected  @endif  >En Cours</option>
                            <option value="validated" @if ($dayOff->result == "validated") selected  @endif>Accepté</option>
                            <option value="refused" @if ($dayOff->result == "refused") selected  @endif>Refusé</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex justify-content-end">
                    Congé de {{ $dayOff->applicant->fullname }}
                </div> 
                @if ($dayOff->applicant_id != $dayOff->author_id)
                    <div class="d-flex justify-content-end">
                        Demandé par {{ $dayOff->author->fullname }}
                    </div>
                @endif
                <div class="d-flex justify-content-end mb-3">
                    Le {{ $dayOff->created_at->translatedFormat("d M Y") }}
                </div>
                @if($dayOff->result == "validated")
                    <div class="d-flex justify-content-end mb-2">
                        @php
                            echo anchor( url("/dayOff/export/pdf/$dayOff->id/0"), "Exporté en pdf",  ["title" => "Exporté en pdf",  "class"=> "text-primary"]);
                            @endphp
                    </div>
                @endif
                <label class="col-form-label">Description</label>
                <textarea id="reason" class="form-control form-control form-control-solid" data-kt-autosize="true" data-rule-required="true" data-msg-required="@lang('lang.required_input')">{{ $dayOff->reason }}</textarea>
                @if ($dayOff->attachments->count())
                    <h6 class="my-5">Fichier joint</h6>
                    @foreach ($dayOff->attachments as $attachment) 
                        <span class="ml-2"><a href="{{ url('days-off/download-attachment') . "/" . $attachment->id }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset(theme()->getMediaUrlPath() . 'svg/files/upload.svg') }}" alt="" data-toggle="tooltip" data-placement="bottom" title="{{ $attachment->filename }}" height="40px" width="40px"></a></span>
                    @endforeach 
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-end my-5">
            @if (!$dayOff->result_date)
                <div class="d-flex justify-content-end my-5">
                    <button type="submit" class="btn btn-sm btn-light-primary font-weight-bold mx-2">
                        @include('partials.general._button-indicator', ['label' =>"Enregistrer" ,"message" => trans('lang.sending')])
                    </button>
                </div>
            @elseif ($dayOff->result == "validated" && !$dayOff->is_canceled)
                <input type="hidden" name="is_canceled" value="1">
                <div class="d-flex justify-content-end my-5">
                    <button type="submit" class="btn btn-sm btn-light-danger font-weight-bold mx-2">
                        @include('partials.general._button-indicator', ['label' => "Annuler" ,"message" => trans('lang.sending')])
                    </button>
                </div>
            @endif
            @if ($dayOff->result == "validated" )
                <div class="d-flex justify-content-end my-5">
                    <button type="button" onclick="modifier(event)" class=" btn btn-sm btn-light-success font-weight-bold mx-2">
                        @include('partials.general._button-indicator', ['label' =>"Modifier" ,"message" => trans('lang.sending')])
                    </button>
                </div>
            @endif
        </div>
    </div>
</form>


<script>
    $(document).ready(function() {
        $("#modal-form-info").appForm({
            onSuccess: function(response) {
                console.log(response)
                //dataTableUpdateRow(dataTableInstance.dayOffRequested , response.row_id, response.data)
                if (dataTableInstance.my_days_off) {
                    dataTableInstance.my_days_off.ajax.reload();
                }
                if (dataTableInstance.dayOffRequested) {
                    dataTableInstance.dayOffRequested.ajax.reload();
                    // reload gantt 
                    loadGantt();
                }
            },
        });

        function init_date(){
                var format = 'DD/MM/YYYY';
                var monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                var daysOfWeek =['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven','Sam'];
                var deliver = $(".datepicker").daterangepicker({
                    singleDatePicker: true,
                    drops: 'auto',
                    autoUpdateInput: false,
                    autoApply: true,
                    locale: {
                        defaultValue: "",
                        format: format,
                        applyLabel: "{{ trans('lang.apply') }}",
                        cancelLabel: "{{ trans('lang.cancel') }}",
                        daysOfWeek: daysOfWeek,
                        monthNames: monthNames,
                    },
                });
                deliver.on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(format))
                });
            }
        init_date(); 
    });

    function modifier(event)
    {
         event.preventDefault();

        const id = document.querySelector('#id').value;
        const startDate = document.querySelector('#start_date').value;
        const returnDate = document.querySelector('#return_date').value;
        const natureId = document.querySelector('#nature_id').value;
        const reason = document.querySelector('#reason').value;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ url("/days-off/update") }}');
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify({
            id,
            start_date: startDate,
            return_date: returnDate,
            nature_id: natureId,
            reason: reason,
            _token: _token
        }));

        xhr.onload = function() {
            var jsonResponse = JSON.parse(xhr.responseText);
            if (xhr.status === 200) {
                if (jsonResponse.succes == false) {
                    alert(jsonResponse.message);
                } else {
                    alert(jsonResponse.message);
                }
                location.reload();
            } else {
                // alert(xhr.error(););
                alert('Une erreur est survenue lors de la modification de la demande de congé.');
            }
        };
    }
</script>