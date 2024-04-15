
@if ($ticket->assign_to && ($for_user->isIt() || $for_user->isAdmin()) && !$ticket->is_resolved() || ($for_user->id == $ticket->proprietor_id && !$ticket->is_resolved()) )
<div  data-bs-placement="bottom" data-bs-toggle="tooltip" data-bs-original-title="Résolue ?"  class="form-check  form-check-custom form-check-solid form-check-sm">
    <button type="submit" id="button-resolve-ticket-{{$ticket->id }}" class="btn btn-sm btn-white " >
    @php
        $is_cheked = "";
        if ( $ticket->is_resolved() ) {
            $is_cheked  = "checked";
        }
        $input = '<input '.$is_cheked.' style="border: 1px solid;  border-color: #50cd89;" class="form-check-input resolve-ticket-input " type="checkbox"  data-ticket-id="'. $ticket->id.'"/>'
    @endphp
        @include('partials.general._button-indicator', ['label' => $input ,"message" => "<span class='indicator-progress'><span class='spinner-border spinner-border-sm align-middle ms-2'></span></span>"])
    </button>
</div>
@endif
