<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Heure debut</th>
            <th>Heure fin</th>
            <th>Type</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($details as $detail)
            <tr>
                <td>{{ $detail->day }}</td>
                <td>{{ $detail->getEntryTime() }}</td>
                <td>{{ $detail->getExitTime() }}</td>
                <td>{{ $detail->additionalHourType->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="card-footer d-flex justify-content-end">
    <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
    <button type="submit" id="submit" class=" btn btn-sm btn-light-primary  mr-2">
        @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" => trans('lang.sending')])
    </button>
</div>