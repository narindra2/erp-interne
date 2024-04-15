<div class="card-body ">
    <div class="mb-2">
        <div class="mh-300px scroll-y me-n7 pe-7">
            <div class="table-responsive">
                <table id="usersWhoSawMessage" class="table table-row-dashed align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Personne</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messagesSeen as $messageSeen)
                            <tr>
                                <td>{{ $messageSeen->user->fullname }}</td>
                                <td>{{ $messageSeen->created_at->translatedFormat('d M, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#usersWhoSawMessage").DataTable({
            dom: "lrtp"
        });
    });
</script>