@php
    $auth = auth()->user();
@endphp
@if ($auth->isHR() || $auth->isAdmin())
    @php
        $users = App\Models\User::select(["name","firstname"])->where('hiring_date', '<=', Carbon\Carbon::now()->subdays(App\Models\ContractType::$_PE_END_DAY_AFTER_HIRING_DATE))
                                ->whereHas('userJob', function ($query) {
                                    $query->where('contract_type_id', App\Models\ContractType::$_PE_CONTRAT);
                                })
                                ->whereDeleted(0)
                                ->get();
        $pe_user = "";
        $count = $users->count();
        foreach ($users as $user) {
            $pe_user  =  $pe_user ? $pe_user.", ".$user->sortname : $user->sortname;
        }
        $verb =   $count == 1 ? "a" : "ont";
        $possessif =   $count == 1 ? "sa" : "leur";
    @endphp
    @if ($count)
        <script>
            var message = "{{ $pe_user }} <u> {{ $verb }} terminé {{ $possessif }} période essaie.</u>"
            toastr.options.timeOut = 0;
            toastr.options.closeButton = false;
            message =   message + ' <br> <a href="'+url('/users')+'" target="_blank"><u>Voir ...</u></a>'
            toastr.info(message, "Periode d'essai Teminé");
        </script>
    @endif
    
    @php
        $nbDaysOffWithoutResponse = App\Models\DayOff::countDayOffWithoutResponse();
    @endphp
    @if ($nbDaysOffWithoutResponse > 0)
        <script>
            var message = "{{ $nbDaysOffWithoutResponse }} demandes de congés en attente.";
            toastr.options.timeOut = 0;
            toastr.options.closeButton = false;
            message =   message + ' <br> <a href="'+url('/days-off')+'" target="_blank"><u>Voir ...</u></a>'
            toastr.info(message, "Congé et Permission");
        </script>
    @endif
@endif 

{{-- <script>
    $(document).ready(function(){
        setTimeout(() => {

            $.ajax({
            url: url("/notification/check/permanent"),
            data: {"_token" : _token },
            type: 'POST',
            success: function(response) {
                console.log(response);
                if (response.success) {
                    var message = response.message;
                    toastr.options.timeOut = 0;
                    toastr.options.closeButton = false;
                    toastr.info("", message);
                }
            },
        });
        }, 3000);
    })
</script> --}}
