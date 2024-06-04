@php
    $count = auth()->user()->get_tickets_not_resoved()
@endphp
@if ( $count )
    <script>
        var message = "Vous aves {{   $count  }} ticket non resolue" 
        toastr.options.timeOut =  0;
        toastr.options.closeButton =  false;
        toastr.info(message, "Alert");
    </script>
@endif
