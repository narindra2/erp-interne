<div>
    @foreach ($contacts as $contact)
        @include('messaging.modals.contact', ['contact' => $contact])
    @endforeach
</div>