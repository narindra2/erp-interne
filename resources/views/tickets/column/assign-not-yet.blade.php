
<div class="d-flex align-items-center">
    <div class="symbol symbol-35px symbol-circle">
        <img alt="Pic" src="{{ $user->avatarUrl }}"> 
    </div>
    <div class="ms-5">
        <b href="#" class="fs-5 fw-bolder text-primary mb-2">
            {{$user->sortname}} <span class="badge badge-success fw-bolder fs-8 px-2 py-1 ms-2">{{ $user->userJob->department->name }}</span> 
        </b>
        <div class="fw-bold text-muted">{{$user->email}}</div>
    </div>
</div>
