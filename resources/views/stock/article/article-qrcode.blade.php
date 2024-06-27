<div id="qrcode-img-{{ $item->id }}"
    class=" mb-2 symbol d-none symbol-100px symbol-lg-160px symbol-fixed position-relative">
    {{ $item->qrcode }}
</div>
<div class="form-check form-switch form-check-custom mt-2 justify-content-center " title="Relever le Qrcode du {{ $item->code_detail }}">
    <input class="form-check-input h-15px w-30px" type="checkbox" role="switch" value="1" che
        id="show-qrcode-{{ $item->id }}">
    <label class="form-check-label" for="qrcode"></label>
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