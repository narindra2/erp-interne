@if(isset($user))
    <form class="form" id="delete-modal-form" method="POST" action="{{ url('/user/delete') }}">
@elseif(isset($customer))
    <form class="form" id="delete-modal-form" method="POST" action="{{ url('/cerfa/customer/delete') }}">
@endif
    <div class="card-body ">
        @csrf
        @if(isset($user))
            <input type="hidden" name="user_id" value="{{ $user->id }}">
        @elseif(isset($customer))
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
        @endif
        <p>
            Vous voulez vraiment supprimer  <a href="#" class="fs-3">
                @if(isset($user))
                    {{ $user->fullname }}
                @elseif(isset($customer))
                    {{ $customer->fullname }}
                @endif </a>
            ?
        </p>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-success btn-sm mr-2 ">
            @lang('lang.no')</button>
        <button type="submit" id="submit" class=" btn btn-sm btn-light-danger  mr-2">
            @include('partials.general._button-indicator', ['label' => trans('lang.yes'),"message" =>
            trans('lang.sending')])
        </button>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#delete-modal-form").appForm({
            showAlertSuccess: true,
            onSuccess: function(response) {
                @if(isset($customer))
                    dataTableInstance.customerList.ajax.reload();
                @endif
            },
        })

    })
</script>
