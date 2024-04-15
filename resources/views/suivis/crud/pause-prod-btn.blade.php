<form  id="pause-prod-form" method="POST" action="{{ url('/suivi/pause/prod') }}">
    @csrf
    <input id="pause_last_id" type="hidden" name="pause_last_id"  value="{{ isset($pause) ? $pause->id : null  }}">
    <div>
        <button type="submit" id="submit-status-prod" class="btn btn-sm btn-{{ ($pause && $pause->status == "pause") ? "warning" : "success" }}  mr-2">
            @include('partials.general._button-indicator', [ "id" => "indicator-label-statut-prod" ,'label' => ($pause && $pause->status == "pause") ? "<i class='fas fa-folder-open'></i>  En attente de dossier" :   "<i class='fas fa-folder'></i> En traitement de dossier"  ,"message" => "Traitement ..."])
        </button>
    </div>
</form>
&nbsp;&nbsp;&nbsp;
<script>
    $(document).ready(function() {
        $("#pause-prod-form").appForm({
            submitBtn : "#submit-status-prod",
            onSuccess : function(response){
                let btn = $("#submit-status-prod")
                let indicator  = $("#indicator-label-statut-prod")
                console.log(response);
                if (response.data.status == "pause") {
                    btn.removeClass("btn-success").addClass("btn-warning")
                    indicator.html("<i class='fas fa-folder-open'></i> En attente de dossier")
                }else{
                    btn.removeClass("btn-warning").addClass("btn-success")
                    indicator.html("<i class='fas fa-folder'></i> En traitement de dossier")
                }
                $("#pause_last_id").val(response.data.id)
            }
        })
    })
</script>


