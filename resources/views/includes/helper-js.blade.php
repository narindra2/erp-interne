<script>
    var app = {};
    app.baseUrl = '{{ env("APP_URL") }}';
    app.assetUrl = '{{  env("APP_ASSET") }}';
    var _token = $('meta[name="csrf-token"]').attr('content')
    app._token = _token;
    
    var getCsrfToken = function() {
        if (!_token) {
            return console.log("Token is not set")
        }
        return _token
    }

    function url(url = "") {
        return "{{ url('') }}" + url
    }
    var dataTableInstance = {}
    var kanbanInstance = null;
    var loopMinutors = {};
    function defaut_toast_config() {
        return toastr.options = {
            "closeButton": true,
            "newestOnTop": 1,
            "progressBar": true,
            "positionClass": "toastr-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "preventDuplicates": 0,
            "timeOut": 5000,
            "showEasing": "swing",
            "hideEasing": "linear",
            "rtl ": true,
        }
    }
    defaut_toast_config();
    var app_lang = {}
    app_lang.undo = "{{ trans('lang.cancel') }}"
    app_lang.message = "{{ trans('lang.message') }}"
    app_lang.processing = "{{ trans('lang.processing') }}"
    app_lang.loading = "{{ trans('lang.loading') }}"
    app_lang.please_wait = "{{ trans('lang.please_wait') }}"
    app_lang.sending = "{{ trans('lang.sending') }}"
    app_lang.error = "{{ trans('lang.error') }}"
    app_lang.try_again = "{{ trans('lang.try_again') }}"
    app_lang.email_required = "{{ trans('lang.email_required') }}"
    app_lang.password_required = "{{ trans('lang.password_required') }}"
    app_lang.detected_error = "{{ trans('lang.detected_error') }}"
    app_lang.got_it = "{{ trans('lang.got_it') }}"
    app_lang.valid_email = "{{ trans('lang.valid_email') }}"
    app_lang.valid_password = "{{ trans('lang.valid_password') }}"
    app_lang.success_login = "{{ trans('lang.success_login') }}"
    app_lang.login_error = "{{ trans('lang.login_error') }}"
    app_lang.ok = "{{ trans('lang.ok') }}"

    function dataTableaddRowIntheTop(tableInstance, data, draw = false) {
        var table = tableInstance
        var currentPage = table.page();
        table.row.add(data).draw(draw)
        var index = table.row(0).index(),
            rowCount = table.data().length - 1,
            insertedRow = table.row(rowCount).data(),
            tempRow;
        for (var i = rowCount; i > index; i--) {
            tempRow = table.row(i - 1).data();
            table.row(i).data(tempRow);
            table.row(i - 1).data(insertedRow);
        }
        table.page(currentPage).draw(draw);
    }

    function dataTableUpdateRow(tableInstance, row_id, data, draw = false) {
        if (data.DT_RowId && row_id != data.DT_RowId) {
            $("#" + row_id).attr("id", data.DT_RowId);
            row_id = data.DT_RowId;
        }
        tableInstance.row("#" + row_id).data(data).draw(draw);
    }

    function dataTableRowDetails(data = []) {
        let rows = "";
        for (const [key, value] of Object.entries(data.row_details)) {
            rows = rows + `<tr id ="${data.DT_RowId}">
                        <td class ="text-start text-gray-400 fw-bolder">- ${key}  :</td>
                        <td>${value}</td>
                    </tr>`
        }
        return ('<table cellpadding="4" cellspacing="0" border="0" style="padding-left:50px;">' + rows + '</table>');
    }
    function dataTableShowRowDetails(tableId = "",dataTableInstance = null ,classDetails ="details-row") {
        $(''+tableId+' tbody').on('click', 'tr td.'+classDetails, function() {
            console.log("yess 2");
            var tr = $(this).closest('tr');
            var row =dataTableInstance.row(tr);
            if (row.child.isShown()) {
                // $(this).html('<i class="fas fa-plus text-hover-primary" title ="Autre detail" style ="cursor:pointer"></i>')
                $(this).html('<i class="fas fa-angle-right" title ="Autre detail" style ="cursor:pointer"></i>')
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // $(this).html('<i class="fas fa-minus" title ="Masquer detail" style ="cursor:pointer"></i>')
                $(this).html('<i class="fas fa-angle-down" title ="Masquer detail" style ="cursor:pointer"></i>')
                row.child(dataTableRowDetails(row.data())).show();
                tr.addClass('shown');
            }
        });
    }
    function scrollBotton(target, vitesse = 2000) {
        $(target).animate({
            scrollTop: $(target)[0].scrollHeight
        }, vitesse);
    }

    function secondsToDhms(seconds) {
                seconds = Number(seconds);
                var d = Math.floor(seconds / (3600 * 24));
                var h = Math.floor(seconds % (3600 * 24) / 3600);
                var m = Math.floor(seconds % 3600 / 60);
                var s = Math.floor(seconds % 60);
                return ((d < 10) ? "0" + d : d) + ":" + ((h < 10) ? "0" + h : h) + ":" + ((m < 10) ? "0" + m : m) +
                    ":" + ((s < 10) ? "0" + s : s);
       }
</script>
