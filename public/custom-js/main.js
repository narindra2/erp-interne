

$(document).ready(function () {
    var intervalID;

    //custom app form controller
    var blockLoader = '<div  class="blockui-message"><span class="spinner-border text-primary"></span> ' + app_lang.please_wait + ' <span id ="upload-info"></span></div>'
    var blockToMask = document.querySelector("#ajax-modal-content");
    var blockUI = new KTBlockUI(blockToMask, { message: blockLoader, });
    (function ($) {
        $.fn.appForm = function (options) {
            var defaults = {
                ajaxSubmit: true,
                submitBtn: "#submit",
                isModal: true,
                forceBlock: false,
                showProgress: true,
                dataType: "json",
                showAlertSuccess: true,
                onModalClose: function () {
                },
                onSuccess: function () {
                },
                onError: function () {
                    return true;
                },
                onFail: function () {
                    return true;
                },
                onSubmit: function () {
                },
                onAjaxSuccess: function () {
                },
                beforeAjaxSubmit: function (data, self, options) {
                }
            };
            var settings = $.extend({}, defaults, options);
            var handleSubmitbutton = function (submitButton, isSubmit = true) {

                $("#ajax-modal-callback").html("");
                if (isSubmit) {
                    $("#ajax-modal-callback").css("display", "none");
                    submitButton.attr('data-kt-indicator', "on");
                    submitButton.attr('disabled', true);
                } else {
                    $("#ajax-modal-callback").css("display", "");
                    submitButton.attr('disabled', false);
                    submitButton.removeAttr('data-kt-indicator');
                }
            }
            this.each(function (e) {
                if (settings.ajaxSubmit) {
                    validateForm($(this), function (form) {
                        var submitButton = $(settings.submitBtn)
                        settings.onSubmit();
                        $(form).ajaxSubmit({
                            dataType: settings.dataType,
                            beforeSubmit: function (data, self, options) {
                                handleSubmitbutton(submitButton, true)
                                settings.beforeAjaxSubmit(data, self, options);
                                if (settings.isModal || settings.forceBlock) {
                                    blockUI.block();
                                }
                            },
                            success: function (result) {
                                handleSubmitbutton(submitButton, !true)

                                settings.onAjaxSuccess(result);
                                if (result.success) {
                                    settings.onSuccess(result);
                                    if (settings.isModal) {
                                        blockUI.release();
                                        if (settings.showProgress) {
                                            $("#upload-info").html('<span class="text-primary">' + "ok" + ' </span>')
                                        }
                                        closeAjaxModal(true);
                                        if (settings.showAlertSuccess) {
                                            if (result.info) {
                                                toastr.error(result.message);
                                            } else {
                                                toastr.success(result.message);
                                            }
                                        }
                                    } else {
                                        if (settings.forceBlock) {
                                            blockUI.release();
                                        }
                                        if (settings.showAlertSuccess) {
                                            toastr.success(result.message);
                                        }
                                    }

                                } else {
                                    if (settings.onError(result)) {
                                        if (settings.isModal) {
                                            blockUI.release();
                                            unmaskModal();
                                            if (result.message) {
                                                $('#ajax-modal-callback').attr('class', 'modal-body')
                                                let messageValidation = result.message;
                                                if (Array.isArray(result.message)) {
                                                    messageValidation = "";
                                                   for (let index = 0; index < result.message.length; index++) {
                                                    messageValidation +=  result.message[index] + "<br>";
                                                   }
                                                }
                                                return appAlert.error(messageValidation, { container: '#ajax-modal-callback', animate: false });
                                            }
                                        } else if (result.message) {
                                            if (settings.forceBlock) {
                                                blockUI.release();
                                            }
                                            $('#ajax-modal-callback').attr('class', 'modal-body')
                                            return toastr.error(result.message);
                                        }
                                    }
                                    return toastr.error(result.message);
                                }
                            },
                            uploadProgress: function (event, position, total, percentComplete) {
                                if (settings.showProgress) {
                                    var progress_class = "primary"
                                    if (percentComplete > 90) {
                                        progress_class = "success"
                                    }
                                    var uploadprogress = '<span class="text-' + progress_class + '"> ' + percentComplete + '% </span>'
                                    $("#upload-info").html(uploadprogress)
                                }


                            },
                            error: function (request, status, error) {
                                settings.onFail(request,status,error);
                                handleSubmitbutton(submitButton, false)
                                blockUI.release();
                                toastr.options.timeOut = 0
                                var messageDetail
                                if (typeof request.responseJSON != "undefined") {
                                    messageDetail = request.responseJSON.message + " File : " + request.responseJSON.file + " Line : " + request.responseJSON.line
                                }
                                else {
                                    messageDetail = error
                                }
                                var uploadprogress = '<span class="text-danger">' + app_lang.error + '</span>'
                                $("#upload-info").html(uploadprogress)
                                toastr.error("<p> Status : " + request.status + " </p> <p> MessageError : " + messageDetail + " </p>");
                            }
                        });
                    });
                } else {
                    validateForm($(this));
                }
            });
            /*
             * @form : the form we want to validate;
             * @customSubmit : execute custom js function insted of form submission. 
             * don't pass the 2nd parameter for regular form submission
             */
            function validateForm(form, customSubmit) {
                //add custom method
                // $.validator.addMethod("greaterThanOrEqual",
                //     function (value, element, params) {
                //         var paramsVal = params;
                //         if (params && (params.indexOf("#") === 0 || params.indexOf(".") === 0)) {
                //             paramsVal = $(params).val();
                //         }
                //         if (!/Invalid|NaN/.test(new Date(value))) {
                //             return new Date(value) >= new Date(paramsVal);
                //         }
                //         return isNaN(value) && isNaN(paramsVal)
                //             || (Number(value) >= Number(paramsVal));
                //     }, 'Must be greater than {0}.');
                $(form).validate({
                    submitHandler: function (form) {
                        if (customSubmit) {
                            customSubmit(form);
                        } else {
                            return true;
                        }
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    unhighlight: function (element) {
                        $(element).closest('.form-group').removeClass('has-error');
                    },
                    errorElement: 'span',
                    errorClass: 'fv-plugins-message-container invalid-feedback help-block mt-2',
                    ignore: ":hidden:not(.validate-hidden)",
                    errorPlacement: function (error, element) {
                        if (element.parent('.input-group').length) {
                            error.insertAfter(element.parent());
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
                //handeling the hidden field validation like select2
                $(".validate-hidden").click(function () {
                    $(this).closest('.form-group').removeClass('has-error').find(".help-block").hide();
                });
            }

            //show loadig mask on modal before form submission;
            function maskModal($maskTarget) {
                var padding = $maskTarget.height() - 80;
                if (padding > 0) {
                    padding = Math.floor(padding / 2);
                }
                var loader = '<div class="text-center"><div class="spinner-border  text-primary" role="status"></div><div id="upload-info" style ="z-index:999"class="text-center"></div></div>'
                $maskTarget.append("<div class='modal-mask'>" + loader + "</div> ");

                //check scrollbar
                var height = $maskTarget.height();

                $('.modal-mask').css({ "width": $maskTarget.width() + 30 + "px", "height": height + "px", "padding-top": padding + "px", "z-index": 999 });
                $maskTarget.closest('.modal-dialog').find('[type="submit"]').attr('disabled', 'disabled').addClass("");
            }

            //remove loadig mask from modal
            function unmaskModal() {
                var $maskTarget = $("#ajax-modal-content");
                $maskTarget.find('[type="submit"]').removeAttr('disabled');
                $(".modal-mask").remove();
            }

            //colse ajax modal and show success check mark
            function closeAjaxModal(success) {
               
                if (success) {
                    $(".modal-mask").html("<div class='circle-done'><i class='fa fa-check'></i></div>");
                    setTimeout(function () {
                        $(".modal-mask").find('.circle-done').addClass('ok');
                    }, 30);
                }
                setTimeout(function () {
                    $(".modal-mask").remove();
                    $("#ajax-modal").find('.modal-dialog').removeClass("modal-lg");
                    $("#ajax-modal").find('.modal-dialog').removeClass("modal-sm");
                    $("#ajax-modal").find('.modal-dialog').removeClass("modal-fullscreen");
                    $("#ajax-modal").modal('hide');
                    $("#ajax-modal-content").removeClass("blockui").html("").attr("style", "");
                    settings.onModalClose();

                }, 1000);
            }
        };
    })(jQuery);
// appAlert
var appAlert = {
    info: info,
    success: success,
    warning: warning,
    error: error,
    options: {
        container: "body", // append alert on the selector
        duration: 0, // don't close automatically,
        showProgressBar: true, // duration must be set
        clearAll: true, //clear all previous alerts
        animate: true //show animation
    }
};

// return appAlert;

function info(message, options) {
    this._settings = _prepear_settings(options);
    this._settings.alertType = "info";
    _show(message);
    return "#" + this._settings.alertId;
}

function success(message, options) {
    this._settings = _prepear_settings(options);
    this._settings.alertType = "success";
    _show(message);

    return "#" + this._settings.alertId;
}

function warning(message, options) {
    this._settings = _prepear_settings(options);
    this._settings.alertType = "warning";
    _show(message);
    return "#" + this._settings.alertId;
}

function error(message, options) {
    this._settings = _prepear_settings(options);
    this._settings.alertType = "error";
    _show(message);
    return "#" + this._settings.alertId;
}

function _template(message) {
    var className = "info";
    var icon = '<i class=" fas fa-exclamation"></i>'
    if (this._settings.alertType === "error") {
        className = "danger";
        icon = '<i class=" fas fa-exclamation-triangle"></i>'
    } else if (this._settings.alertType === "success") {
        className = "success";
        icon = '<i class="nav-icon far fa-check-circle"></i>'
    } else if (this._settings.alertType === "") {
        className = "waring";
        icon = '<i class=" nav-icon fas fa-exclamation-circle"></i>'
    }

    if (this._settings.animate) {
        className += " animate";
    }

    return '<div class="alert alert-dismissible bg-light-' + className + ' border border-danger border-dashed d-flex flex-column flex-sm-row w-100 p-5 mb-2">'
        + '<div class="d-flex flex-column pe-0 pe-sm-10">'
        + '<h5 class="mb-1"> Messsage(s) : </h5>'
        + '<span> ' + message + ' </span>'
        + '</div>'

        + '<button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">'
        + '<i class="bi bi-x fs-1 text-' + className + '"></i>'
        + '</button>'
        + '</div>'
}

function _prepear_settings(options) {
    if (!options)
        var options = {};
    options.alertId = "app-alert-" + _randomId();
    return this._settings = $.extend({}, appAlert.options, options);
}

function _randomId() {
    var id = "";
    var keys = "abcdefghijklmnopqrstuvwxyz0123456789";
    for (var i = 0; i < 5; i++)
        id += keys.charAt(Math.floor(Math.random() * keys.length));
    return id;
}

function _clear() {
    if (this._settings.clearAll) {
        $("[role='alert']").remove();
    }
}

function _show(message) {
    _clear();
    var container = $(this._settings.container);
    if (container.length) {
        if (this._settings.animate) {
            //show animation
            setTimeout(function () {
                $(".app-alert").animate({
                    opacity: 1,
                    right: "40px"
                }, 500, function () {
                    $(".app-alert").animate({
                        right: "15px"
                    }, 300);
                });
            }, 20);
        }

        $(this._settings.container).prepend(_template(message));
        _progressBarHandler();
    } else {
        console.log("appAlert: container must be an html selector!");
    }
}

function _progressBarHandler() {
    if (this._settings.duration && this._settings.showProgressBar) {
        var alertId = "#" + this._settings.alertId;
        var $progressBar = $(alertId).find('.progress-bar');

        $progressBar.removeClass('hide').width(0);
        var css = "width " + this._settings.duration + "ms ease";
        $progressBar.css({
            WebkitTransition: css,
            MozTransition: css,
            MsTransition: css,
            OTransition: css,
            transition: css
        });

        setTimeout(function () {
            if ($(alertId).length > 0) {
                $(alertId).remove();
            }
        }, this._settings.duration);
    }
}
    // reset ajax modal to defaut
    $('#ajax-modal').on('hidden.bs.modal', function (e) {

        $("#modal-dialog").attr("class", "modal-dialog modal-dialog-centered")
        $(this).find('.modal-dialog').addClass("modal-sm");
        $("#ajax-modal-original-content").css("display", "")
        $("#ajax-modal-callback").html("");

    })

    var ajaxDrawerEl = document.querySelector("#ajax-drawer");
    var ajaxDrawer = KTDrawer.getInstance(ajaxDrawerEl);
    var t = document.querySelector("#ajax-drawer-body");
    var u = new KTBlockUI(t, { message: blockLoader });
    $('body').on('click', '[data-act="ajax-drawer"]', function (e) {
        ajaxDrawer.destroy();
        var data = { "_token": getCsrfToken() },
            url = $(this).attr('data-action-url'),
            title = $(this).attr('data-title'),
            $this = $("#ajax-drawer");
        if (!url) {
            console.log('Ajax drawer: Set data-action-url!');
            return false;
        }
        if (title) {
            $("#ajax-drawer-title").html(title);
        } else {
            $("#ajax-drawer-title").text($("#ajax-drawer-title").attr('title'));
        }
        console.log(title);
        $(this).each(function () {
            $.each(this.attributes, function () {
                if (this.specified && this.name.match("^data-post-")) {
                    var dataName = this.name.replace("data-post-", "");
                    data[dataName] = this.value;
                }
            });
        });
        if ($(".dropdown-menu")) {
            $(".dropdown-menu").removeClass("show");
        }
        ajaxDrawer.show();
        u.block();
        $.ajax({
            url: url,
            data: data,
            cache: false,
            type: 'POST',
            dataType: "json",
            success: function (response) {
                u.release();
                if (!response.view) {
                    $("#ajax-drawer-content").html(response);
                } else {
                    $("#ajax-drawer-content").html(response.view);
                }
                if (response.info) {
                    $("#ajax-drawer-info").html(response.info);
                }
            },
            statusCode: {
                404: function () {
                    u.release();
                }
            },
            error: function () {
                u.release();
            }
        });
        return false;

    });

    ajaxDrawer.on("kt.drawer.after.hidden", function () {
        $("#ajax-drawer-content").html("");
        $("#ajax-drawer-info").html("");
        if (u.isBlocked()) {
            u.release();
        }
    });
    //bind ajax tab
    // var blockLoaderTab = '<div  class="blockui-message"><span class="spinner-border text-primary"></span> ' + app_lang.please_wait + ' <span id ="upload-info"></span></div>'
    // var blockToMaskTab = document.querySelector("#ajax-tab-content");
    // var blockUITab = new KTBlockUI(blockToMaskTab, { message: blockLoaderTab, });

    $('body').on('click', '[data-toggle="ajax-tab"]', function () {
        var $this = $(this);
        $this.addClass('active')
        loadurl = $this.attr('data-load-url'),
        target = $this.attr('href');
        if (!target || !loadurl || loadurl == "#") {
            return false;
        }
        $(target).html(`<div class="d-flex justify-content-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
            </div>`);
        $.get(loadurl, function (data) {
            $(target).html(data);
            
        });
        $this.show()
    });
    

    $('body').on('click', '[data-action=delete]', function (e) {
        var $target = $(e.currentTarget);
        var tableInstance = $(this).parents('table').attr("id")
        var tr = $(this).parents('tr')
        var reload = $target.attr('data-reload-table');
        if (e.data && e.data.target) {
            $target = e.data.target;
        }
        var targetHtml = $target.html()
        $target.html(' <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>')
        var url = $target.attr('data-action-url'),
            id = $target.attr('data-id');
        var data = { "id": id, _token: _token }
        $(this).each(function () {
            $.each(this.attributes, function () {
                if (this.specified && this.name.match("^data-post-")) {
                    var dataName = this.name.replace("data-post-", "");
                    data[dataName] = this.value;
                }
            });
        });
        var posts = ""
        for (key in data) {
            posts += " data-post-" + key + "=" + data[key];
        }
        
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success) {
                    if (typeof result.row_id != "undefined" && typeof result.data != "undefined") {
                        dataTableUpdateRow(dataTableInstance[tableInstance], result.row_id, result.data);

                    } else {
                        dataTableInstance[tableInstance].row(tr).remove().draw();
                        tr.fadeOut("slow")
                    }
                    if (typeof result.extra_data != "undefined") {
                        dataTableUpdateRow(dataTableInstance[result.extra_data.table], result.extra_data.row_id, result.extra_data.data);
                    }

               
                    toastr.warning("<span data-table-instance-id-call-back-delete = " + tableInstance + ">" + result.message + "</span> <span title=" + app_lang.undo +
                    " style='color:black' data-action-url=" + url + " data-id =" + id + "data-reload-table="  + reload +
                    " data-action='cancel-deleted' data-table-instance-id-call-back-delete=" + tableInstance + " " + posts + "  >" + (result.can_not_undo ? "" : "annuler") + "</span>");
                    
                } else {
                    toastr.options.timeOut = 0;
                    toastr.error("<span data-table-instance-id-call-back-delete = " + tableInstance + ">" + result.message + "</span>");
                }
                $target.html(targetHtml)
            },
            error: function (request, status, error) {
                toastr.options.timeOut = 0
                toastr.error("<span> Status : " + request.status + "MessageError : " + request.responseJSON.message ? request.responseJSON.message : error + "File : " + request.responseJSON.file + "Line : " + request.responseJSON.line + " </span>");

            }
        });


    });
    // $('body').on('click', '[data-act="data-mail-to"]', function (e) {
    //     var title = $(this).attr('data-title');
    //     if (title) {
    //         $("#data-mail-to").html(title);
    //     } else {
    //         $("#data-mail-to-title").text($("#data-mail-to-title").attr('title'));
    //     }
    //     var mail = $(e.target).text();
    //     if ($("#data_mail").is(":visible")) {
    //         if (isMail(mail)) {
    //             $("#mail_value").val(mail);
    //             setTimeout(function () { $('#mail_to').trigger("click") }, 100);
    //             setTimeout(function () { $("#email_content").focus(); window.scrollTo(0, document.body.scrollHeight); }, 100);
    //         } else {
    //             $('#mail_to').trigger("click");
    //         }
    //     } else {
    //         $("#mail_value").val(mail);
    //         setTimeout(function () { $('#mail_to').trigger("click") }, 100);
    //         setTimeout(function () { $("#email_content").focus(); window.scrollTo(0, document.body.scrollHeight); }, 100);
    //     }
    // });

    function isMail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    // delete row dataTable
    $('body').on('click', '[data-action=cancel-deleted]', function (e) {
        var $target = $(this)
        var url = $target.attr('data-action-url'),
            id = $target.attr('data-id');
            reload = $target.attr('data-reload-table');
            
        var data = { "id": id, _token: _token, cancel: true }
        $(this).each(function () {
            $.each(this.attributes, function () {
                if (this.specified && this.name.match("^data-post-")) {
                    var dataName = this.name.replace("data-post-", "");
                    data[dataName] = this.value;
                }
            });
        });
        tableInstance = $target.attr("data-table-instance-id-call-back-delete")
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.success) {
                    if (reload) {
                        return dataTableInstance[tableInstance].ajax.reload();  
                    }
                    if (typeof result.row_id != "undefined" && typeof result.data != "undefined") {
                        dataTableUpdateRow(dataTableInstance[tableInstance], result.row_id, result.data);
                        if (typeof result.extra_data != "undefined") {
                            dataTableUpdateRow(dataTableInstance[result.extra_data.table], result.extra_data.row_id, result.extra_data.data);
                        }
                    } else {
                        // dataTableInstance[tableInstance].row(tr).remove().draw();
                        // tr.fadeOut("slow")
                        dataTableInstance[tableInstance].row.add(result.data).draw()
                    }

                    toastr.success(result.message);
                }

            },
            error: function (request, status, error) {
                toastr.options.timeOut = 0
                toastr.error("<span> Status : " + request.status + "MessageError : " + request.responseJSON.message ? request.responseJSON.message : error + "File : " + request.responseJSON.file + "Line : " + request.responseJSON.line + " </span>");
            }
        });
    })

    $('body').on('click', '[data-act="data-mail-to"]', function(e) {
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

    if ($('[data-toggle="ajax-tab"]')[0]) {
        $('[data-toggle="ajax-tab"]')[0].click();
    }

});
