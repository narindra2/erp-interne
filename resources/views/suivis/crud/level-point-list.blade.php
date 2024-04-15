<div class="row">
    <div class="col-md-6"> <label for="type" class="form-label">Niveaux </label></div>
    <div class="col-md-6"> <label for="type" class="form-label">Point </label></div>
</div>
@foreach ($level_points as $item)
    <div  id="level-point-{{ $item->id }}" class="input-group input-group-solid mb-2 level-point-input">
        <input type="text" name="difficulties[]" autocomplete="off" class="form-control col-md-3"
            value="{{ $item->level }}" placeholder="Niveau/difficuté ...">
        <span class="input-group-text"><i class="fas fa-arrow-right"></i></span>
        <input type="text" name="points[]" autocomplete="off" class="form-control col-md-3"
            value="{{ $item->point }}" placeholder="Point ... ">
        <button type="button" onClick="del(this,{{ $item->id }})" class="btn btn-sm btn-icon btn-light-danger col-1 "><i  class="la la-trash-o"></i></button>
    </div>
@endforeach

<div id="clonable" class="input-group input-group-solid mb-2 level-point-input">
    <input type="text" name="difficulties[]" autocomplete="off" class="form-control"
        placeholder="Niveau/difficuté ...">
    <span class="input-group-text"><i class="fas fa-arrow-right"></i></span>
    <input type="text" name="points[]" autocomplete="off" class="form-control" placeholder="Point ... ">
    <button type="button" onClick="del(this)" class="btn btn-sm btn-icon btn-light-danger col-1 "><i class="la la-trash-o"></i></button>
</div>

<div class="col-lg-4" id="add" onClick="add()">
    <a href="javascript:;" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
        <i class="la la-plus"></i> &nbsp;</a>
</div>
<script>
    var min = 1
    var maxInput = 10

    function del(params,id = 0) {
        if (id) {
            $("#level-point-"+id).remove();
        }
        if (min > 1) {
            min--
            $(params).closest('.level-point-input').remove();
        }
    }
    function add() {
        if (min < maxInput) {
            min++
            $("#clonable").clone().insertAfter(".level-point-input:last").find("input[type='text']").val("");
        }
    }
</script>

{{-- <div class="row mb-2 ">
    <div class="col-lg-4" id="add" onClick="add()">
        <a href="javascript:;" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
            <i class="la la-plus"></i></a>
    </div>
</div>

<div class="row  mb-1 file-input" id="div">
    <div class="col-md-11">
        <input class="form-control form-control-sm" name="files[]" type="file">
    </div>
    <button type="button" onClick="del(this)" class="btn btn-sm btn-icon btn-light-danger col-1 "><i
            class="la la-trash-o"></i></button>
</div> --}}
