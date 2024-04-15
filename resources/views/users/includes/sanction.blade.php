<div class="separator separator-dashed my-8"></div>
<h3 class="card-title">Sanction</h3>

<div class="form-group row py-4">
    <label class="col-4 col-form-label">Nombre d'avertissement verbal: <span class="mx-3" id="verbal_warning">{{ $user->verbal_warning }}</span></label>
    <label class="col-4 col-form-label">Nombre d'avertissement écrit: <span class="mx-3" id="written_warning">{{ $user->written_warning }}</span></label>
    <label class="col-4 col-form-label">Nombre de mis à pied: <span class="mx-3" id="layoff">{{ $user->layoff }}</span></label>
</div>

<div class="my-4 d-flex justify-content-end">
    {{-- <a href="{{ url('/users') }}" class="btn btn-sm btn-light-secondary">+ Nouvel avertissement</a> --}}
    @php
        echo modal_anchor(url("/users/sanctions/form/"), '<i class="fas fa-plus"></i>' . ' Nouvel sanction', ['title' => 'Nouvel sanction', 'class' => 'btn btn-sm btn-light-primary', 'data-post-user_id' => $user->id]);
    @endphp
</div>

<table id="sanctionTable" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover "></table>