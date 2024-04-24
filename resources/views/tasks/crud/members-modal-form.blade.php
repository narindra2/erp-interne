<form class="form" id="members-modal-form" method="POST" action="{{ url('/task/section/save/new_members') }}">
    <div class="card-body">
        @csrf
        <input type="hidden" id="section_id" name="section_id" value="{{ $section_id }}">
        <div class="form-group">
            <label for="users-list" class="mb-2">Les collaborateurs :</label>
            <div class="input-group mb-5">
                <span class="input-group-text">
                    <span class="svg-icon svg-icon-2x">
                        <i class="fas fa-users"></i>
                    </span>
                </span>
                @include('tasks.kanban.users-tag', ['users' => $users,"placeholder" => ""])
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-dark btn-sm mr-2 ">
            @lang('lang.cancel')
        </button>
        <button type="submit" id="submit"class=" btn btn-sm btn-light-primary mr-2">
            @include('partials.general._button-indicator', [
                'label' => trans('lang.add'),
                'message' => trans('lang.sending'),
            ])
        </button>
    </div>
</form>
<div class="card  ">
    <label for="">List des membres : </label>
    <table id="taskSectionMembersTable"class="table table-row-dashed  table-hover "></table>
</div>
<script>
    $(document).ready(function() {
        KTApp.initSelect2();
        dataTableInstance.taskSectionMembersTable = $("#taskSectionMembersTable").DataTable({
            processing: true,
            dom: "tr",
            ordering: false,
            columns: [
                { data: "user",title: '', "class": "text-right"},
                { data: "action", title: '' },
            ],
            ajax: {
                url: url("/task/section/members/list"),
                data: function(data) {
                    data.section_id = "{{ $section_id }}";
                }
            },
            language: {
                url: url("/library/dataTable/datatable-fr.json")
            },
        })
        $("#members-modal-form").appForm({
            onSuccess: function(response) {
                let users = response.data
                /**update select form */
                users.forEach(function (user) {
                    var newOption = new Option(user.name, user.id);
                    dataTableaddRowIntheTop(dataTableInstance.taskSectionMembersTable, user)
                    $('#user_id').append(newOption);
                });
                /**update avatar list */
                $(".avatar-member:last").after(response.avatar_html);
            },
        })
    });
</script>
