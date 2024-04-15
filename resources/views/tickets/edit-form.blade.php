<form class="form" id="tikect-modal-form-edit" method="POST" action="{{ url("/ticket/save_edit/$ticket->id")}}">
    <div class="card-body ">
        @csrf
        <div class="form-group">
            <div class="mb-5">
                <label for="proprietor_id" class="form-label">@lang('lang.proprietor')| Demandeur</label>
                <input type="hidden" name="waiting_to_buy" id="waiting_to_buy" value="{{ $waiting_to_buy }}">
                <select disabled name="proprietor_id" class="form-select form-select-solid form-control-lg "   data-dropdown-parent="#ajax-modal" data-control="select2" data-placeholder="@lang('lang.proprietor')"  >
                    <option disabled  value="0"> -- Demandeur --</option>
                    @foreach ($from as $user)
                        <option @if ($ticket->proprietor_id == $user->id) selected @endif value="{{  $user->id  }}">{{  $user->sortname  }}  </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="mb-5">
                <label for="status_id" class="form-label">@lang('lang.status')</label>
                <select  name="status_id" id="status_id_select" class="form-select form-select-solid form-control-lg "  data-rule-required="true" data-msg-required="@lang('lang.required_input')"  data-dropdown-parent="#ajax-modal" data-control="select2" data-placeholder="@lang('lang.status')" >
                    <option disabled status value="0"> -- Statut --</option>
                    @foreach ($status as $s)
                        <option @if ($ticket->status_id == $s["value"]) selected @endif   value="{{ $s["value"]  }}">{{ $s["text"]  }}  </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="separator mb-5"></div>
        <div class="form-group">
            <label for="description" class="form-label">@lang('lang.description')</label>
            <input type="text" list="suggestions" placeholder="Description du demande " value="{{ $ticket->description  }}" name="description" autocomplete="off" multiple class="form-control form-control-lg form-control-solid" data-rule-required="true" data-msg-required="@lang('lang.required_input')">
            <datalist id="suggestions">
                    @foreach ($suggestions as $suggestion)
                        <option  value="{{ $suggestion }}">
                    @endforeach
            </datalist>
        </div>
        <div class="separator separator-dashed my-8"></div>
        <div id="to_buy">
            <h5 class="mb-5">Les matérielles à acheter</h5>

            <div class="d-flex justify-content-end">
                <p class="btn btn-sm btn-light-success" id="addLine">
                    <i class="fas fa-plus"></i>
                </p>
            </div>
            <div class="table-responsive">
                <table class="table table-calcul align-middle table-row-dashed fs-6 gy-5 mb-5">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th>Article</th>
                            <th>Quantité</th>
                            <th>Unité</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold text-gray-600">
                        @if ($needToBuy->count() == 0)
                            <tr class="add_tr">
                                <td>
                                    <select class="form-control form-control-solid" id="itemType" name="item_type_id[]">
                                        <option>-- Article --</option>
                                        @foreach ($itemTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-solid" name="quantity[]" min="0" value="1">
                                </td>
                                <td>
                                    <select class="form-control form-control-solid" id="unit" name="units_id[]">
                                        <option>-- Unité --</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <p class="btn btn-light-danger font-weight-bold mr-2 btn-sm"
                                    onclick="deleteLine(this)">
                                        <i class="far fa-trash-alt"></i>
                                    </p>
                                </td>
                            </tr>
                        @endif
                        @foreach ($needToBuy as $need)
                            <tr class="add_tr">
                                <input type="hidden" name="need_to_buy_id[]" value="{{ $need->id }}">
                                <td>
                                    <select class="form-control form-control-solid" id="itemType" name="item_type_id[]">
                                        @foreach ($itemTypes as $type)
                                            <option value="{{ $type->id }}" @if ($need->item_type_id == $type->id)
                                                selected
                                            @endif>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-solid" name="quantity[]" min="0" value="{{ $need->nb }}">
                                </td>
                                <td>
                                    <select class="form-control form-control-solid" id="unit" name="units_id[]">
                                        <option>-- Unité --</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" @if ($need->unit_item_id == $unit->id)
                                                selected
                                            @endif >{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <p class="btn btn-light-danger font-weight-bold mr-2 btn-sm"
                                    onclick="deleteLine(this)">
                                        <i class="far fa-trash-alt"></i>
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if (!$clearBtn)
        <div class="card-footer d-flex justify-content-end">
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
            <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
                @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" => trans('lang.sending')])
            </button>
        </div>
    @endif
</form>
<script>

    min = 1;
                             
    function deleteLine(content) {
        if (min > 1) {
            min--;
            $(content).closest(".add_tr").remove();
        }
    }

    $(document).ready(function() {
        $("#to_buy").css("display", "none");
        KTApp.initSelect2();

        function showFormItemType() {
            if($("#waiting_to_buy").val() == $("#status_id_select").val()) {
                $("#to_buy").css("display", "");
            }
            else {
                $("#to_buy").css("display", "none");
            }
        }
        showFormItemType();

        $("#status_id_select").on("change", function() {
            showFormItemType();
        });

        $("#addLine").on("click", function() {
            min++;
            $(".add_tr").eq(0).clone().insertAfter(".add_tr:last");
            setTimeout(() => {
              
                KTApp.initSelect2();
            }, 1000);
        });
            
        $("#tikect-modal-form-edit").appForm({
            onSuccess: function(response) {
                if (response.data) {
                    dataTableUpdateRow(dataTableInstance.ticketsTable ,response.row_id, response.data)    
                }
            },
        })
    })
</script>