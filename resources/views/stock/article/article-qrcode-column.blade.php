@if ($item->qrCode)
    <div id="qrcode-img-{{ $item->id }}" class=" mb-2 symbol d-none to-link ">
        {{ $item->qrcode }}
    </div>
    <div class="form-check form-switch form-check-custom justify-content-center " title="Afficher le Qrcode du {{ $item->code_detail }}">
        <input class="form-check-input h-15px w-30px" type="checkbox" role="switch" value="1"  id="show-qrcode-{{ $item->id }}">
        <label class="form-check-label" for="show-qrcode-{{ $item->id }}">QrCode</label>
    </div>
    <script>
        $("#show-qrcode-{{ $item->id }}").on("change",function(){
            if ($(this).is(':checked')){ 
                $("#qrcode-img-{{ $item->id }}").removeClass("d-none")
            }else { 
                $("#qrcode-img-{{ $item->id }}").addClass("d-none")
            }
        });
    </script>  
@else 
    <div class="form-check form-switch form-check-custom justify-content-center " title="Pas de Qrcode du {{ $item->code_detail }}">
        <input disabled class="form-check-input h-15px w-30px" type="checkbox" role="switch" >
        <label class="form-check-label" >QrCode</label>
    </div>
@endif
