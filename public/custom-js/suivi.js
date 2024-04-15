$('body').on('click', '[data-act="data-detail"]', function(e) {
    var title = $(this).attr('data-title');
    if (title) {
        $("#data-mail-to").html(title);
    } else {
        $("#data-mail-to-title").text($("#data-mail-to-title").attr('title'));
    }
    var mail = $(e.target).text();
    if ($("#data_mail").is(":visible")) {
        if (isMail(mail)) {
            $("#mail_value").val(mail)
;
            setTimeout(function() { $('#mail_to').trigger("click") }, 100);
            setTimeout(function() {
                $("#email_content").focus();
                window.scrollTo(0, document.body.scrollHeight);
            }, 100);
        } else {
            $('#mail_to').trigger("click");
        }
    } else {
        $("#mail_value").val(mail)
;
        setTimeout(function() { $('#mail_to').trigger("click") }, 100);
        setTimeout(function() {
            $("#email_content").focus();
            window.scrollTo(0, document.body.scrollHeight);
        }, 100);
    }
});