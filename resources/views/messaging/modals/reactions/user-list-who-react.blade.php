@php
    $users = getUserList();
@endphp

@foreach ($reactions as $reaction)
    <p>{{ $users->where('id', $reaction->user_id)->first()->sortname }}</p>
@endforeach