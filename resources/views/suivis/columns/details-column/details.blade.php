<form action="{{ url("/suivi/more-detail/save")}}" id="more-detail-form" method="POST">
    <div class="card">
        <div class="card-body" style="margin-bottom: -50px;">
            <div class="my-8">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                {{-- <div class="form-group ">
                    <div class="mb-3">
                        <label for="" class=" form-label"> Type de dossier :</label>
                        <div class="input-group mb-5">
                            <select class="form-select form-select-sm  " data-can-edit="true" name="category" id="input-category" data-hide-search="true" data-control="select2">
                                <option value=""  @if (!$item->suivi->category ) selected @endif > -- Type de dossier --</option>
                                @foreach ($clients_type  as $type)
                                    <option value="{{get_array_value($type , "value")}}"   @if ( $item->suivi->category == get_array_value($type , "value") ) selected @endif > {{ get_array_value($type , "text") }} </option>
                                @endforeach
                            </select>
                            
                        </div>
                    </div>
                </div>  --}}
                <div class="form-group ">
                    <div class="mb-3">
                        <label for="times_estimated" class=" form-label"> Temps de traitement estimatif :</label>
                        <div class="input-group mb-5">
                            <input title="Temps de traitement estimatif" type="text" name="times_estimated" id="input-times_estimated" class=" form-control form-control-sm " data-can-edit="false" autocomplete="off"  value="{{ $item->times_estimated  ?? "" }}"placeholder="Temps en heure ex: 4h">
                        </div>
                    </div>
                </div> 
                <div class="form-group ">
                    <div class="mb-3">
                        <label for="" class=" form-label">  Emplacement :</label>
                        <div class="input-group mb-5">
                            <textarea class="form-control" name="folder_location" id="folder_location" data-kt-autosize="true" rows="3" placeholder="Emplacement du dossier  ..."   >{{ $item->suivi->folder_location ?? "" }}</textarea>
                        </div>
                    </div>
                </div> 
                <div class="form-group ">
                    <div class="mb-3">
                        <label for="Date" class=" form-label"> Date début et fin :</label><br>
                        <div class="input-group mb-5">
                            @php
                                $finished_at = $item->finished_at ?? "..."
                            @endphp
                            <input title="Date" readonly  disabled  type="text" class=" form-control form-control-solid "  autocomplete="off" value="{{ ($item->created_at ?? "... ") ." -> ".( $finished_at)  }}" placeholder="{{ ($item->created_at ?? "... ") ." -> ".( $finished_at )  }}">
                        </div>
                    </div>
                </div> 
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end " style=" padding: 1rem 2.25rem;background-color: transparent;">
            <button type="submit" id="submitForm" class=" btn btn-sm btn-light-primary  mr-2">
                @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" =>trans("lang.sending")])
            </button>
        </div>

        
    </div>
</form>
<div class="separator mt-2"></div>
<div class="card shadow-sm ">
    <div class="card-body  ">
        <form action="{{ url("/suivi/more-detail/save-note")}}" id="note-form" method="POST" class="mb-2">
            @csrf
            <input type="hidden" name="suivi_item_id" value="{{ $item->id }}">
            <div class="row">
                <div class="form-group col-md-9 ">
                    <label for="note" class=" form-label"> Ajouter une note de qualité :</label>
                    <div class="input-group ">
                        <input title="Note" type="text" name="note" id="input-note" class=" form-control form-control-sm " data-rule-required="true" data-msg-required="@lang('lang.required_input')" data-can-edit="false" autocomplete="off"  value=""placeholder="Note ex : 15.6 ">
                    </div>
                </div> 
                <div class="form-group col-md-3">
                    <label for="note" class=" form-label"> Ajouter  </label>
                    <button type="submit" id="submitFormNote" class=" btn btn-light-info btn-sm  mr-2">
                        @include('partials.general._button-indicator', ['label' => "+ " . trans('lang.add'),"message" =>trans("lang.sending")])
                    </button>
                </div> 
            </div>
               
        </form>
        @php
            $count_row = 3;
        @endphp
        <table id="noteSuiviItems" class="table table-hover   gs-1 gy-2 gx-1">
            <tfoot>
                <tr>
                    @for ($i = 0; $i <= $count_row; $i++)
                        @if ( $i== 3 )
                            <td id="total_note" class="justify-content-end fs-5"></td>
                        @else
                            <td></td>
                        @endif
                    @endfor
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        dataTableInstance.noteSuiviItems = $("#noteSuiviItems").DataTable({
            processing: true,
            ordering: false,
            dom: "tr",
            columns: [ 
                {data :"actions" , title: '',"class":"text-left"},
                {data :"date" , title: 'Ajouté le',"class":"text-left"},
                {data :"creator" , title: 'Ajouté par',"class":"text-left"},
                {data :"note" , title: 'Note',"class":"text-left"},
                
            ],
            ajax: {
                url: url("/suivi/item/note/data-list"),
                data: function(data) {
                    data.suivi_item_id = {{  $item->id }};
                }
            },
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var monTotal = api
                .column( {{   $count_row }} )
                .data()
                .reduce( function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0 );
                console.log(monTotal);
                $( api.column( 0 ).footer() ).html('Total');
                $('#total_note').html(monTotal.toFixed(2));
            },
        });
        $("#more-detail-form").appForm({
            showAlertSuccess: true,
            submitBtn: "#submitForm",
            onSuccess: function(item) {
                
            },
        })
        $("#note-form").appForm({
            showAlertSuccess: true,
            isModal: false,
            submitBtn: "#submitFormNote",
            onSuccess: function(response) {
               if (response.success) {
                    $("#input-note").val("");
                    dataTableInstance.noteSuiviItems.ajax.reload();
               }
            },
        })
    });
</script>
