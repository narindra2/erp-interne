@php
$id = isset($contact) ? $contact->contact_id : $user->id;
$sortname = isset($contact) ? $contact->user->sortname : $user->id;
// $sortname = 'User';
// if ($contact) {
//     if (isset($contact->user)) {
//         $contact->user->sortname;
//     } else {
//         $sortname = $user->sortname;
//     }
// } else {
//     $sortname = $user->sortname;
// }
@endphp
<a href="#messageContent" class="fs-5 fw-bolder text-gray-900 text-hover-primary mb-2" data-toggle="ajax-tab"
    data-bs-toggle="tab" data-load-url="{{ url("/messaging/discussion/$id") }}">{{ $sortname }}</a>
