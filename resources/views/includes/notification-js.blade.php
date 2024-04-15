<script>
    // initilisation file location /assets/demo1/scripts.js
    function handleNotification(notification = null) {
        console.log(notification)
        if (notification.classification === "bell") {
            incrementBell()
        }
        if (notification.item) {
            addItemNotification(notification)
        }
        if (typeof notification.toast != "undefined") {
            pushPopNotification(notification.toast)
        }
        if (typeof notification.extra_data != "undefined" && notification.extra_data.type == "dataTable") {
            updateTableInstance(notification);
        }
        if (typeof notification.extra_data != "undefined" && notification.extra_data.type == "kanban") {
            addOrUpdateOrDeleteTaskInKanban(notification.extra_data.item)
        }
        if (typeof notification.extra_data != "undefined" && notification.extra_data.type == "message") {
            showBubble(notification.extra_data)
        }
        if (typeof notification.extra_data != "undefined" && notification.extra_data.type == "section_task") {
            addSectionTask(notification.extra_data)
        }
    }

    function incrementBell() {
        var notificationCount = $("#notifications-count").text();
        notificationCount = parseInt(notificationCount)
        notificationCount ? notificationCount++ : (notificationCount = 1);
        $("#notifications-count").text(notificationCount)
        if (!$("#pulse-notification").hasClass("pulse-ring")) {
            $("#pulse-notification").addClass("pulse-ring")
        }
    }

    function addItemNotification(notification = null) {
        if ($(".notification-item").length) {
            $(".notification-item:first").before(notification.item)
        } else {
            $("#notification-item").html(notification.item)
        }
    }

    function pushPopNotification(message) {
        toastr.options.closeDuration = 6000;
        if (message.duration) {
            toastr.options.closeDuration = message.duration;
        }
        if (message.position) {
            toastr.options.positionClass = "toastr-bottom-" + message.position;
        }
        if (message.duration == "forever") {
            toastr.options.timeOut = 0;
            toastr.options.closeButton = false;
        }
        let content = message.content
        if (message.redirect) {
            content = content + '<br> <a href="' + message.redirect + '" target="_blank"><u>Voir ...</u> </a>'
        }
        toastr.info(content, message.title);
        pushDesktopNotification(message)
        defaut_toast_config()
    }

    function addOrUpdateOrDeleteTaskInKanban(item) {
        if (kanbanInstance == null) {
            return;
        }
        var board = kanbanInstance.findBoard(item.board_id);
        if (!board && !item.archived && !item.deleted ) {
            if($("#alert-section-"+item.section_id).hasClass("d-none")) {
                $("#alert-section-"+item.section_id).removeClass("d-none")
            }
            return;
        }
        var old = kanbanInstance.findElement(item.data.id);
        if (old) {
            if(item.deleted) {
                return kanbanInstance.removeElement(old);
            }
            let old_board_id = $(old).closest("div.kanban-board").attr("data-id");
            if (old_board_id == "board-id-"+item.board_id) {
                return kanbanInstance.replaceElement(old, item.data);
            }else{
                kanbanInstance.removeElement(old);
            }
        }
        return kanbanInstance.addElement(item.board_id, item.data);
    }
    function pushDesktopNotification(message) {
        $.ajax({
            url: url("/desktop/notification"),
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": _token,
                "title": message.title,
                "message": message.content
            },
            success: function(result) {
                return true;
            },
        });

    }

    function updateTableInstance(notification) {
        var instanceTable = dataTableInstance[notification.extra_data.table];
        if (!instanceTable) {
            return false;
        }
        var newData = notification.extra_data.row;
        if (typeof notification.extra_data.row_id != "undefined" && $("#" + notification.extra_data.row_id)) {
            var row_id = notification.extra_data.row_id;
            if ($("#" + row_id).length) {
                dataTableUpdateRow(instanceTable, row_id, newData, true);
            } else {
                dataTableaddRowIntheTop(instanceTable, newData, true)
            }
        } else {
            dataTableaddRowIntheTop(instanceTable, newData, true)
        }
    }
  
    function showBubble(data) {
        // alert('ll')
        if (data.group_id != 0) {
            if ($("#chat-group-modal-" + data.group_id).length) {
                let count = $("#chat-private-id-notification-group-count-" + data.group_id).text();
                count = parseInt(count) || 0 + parseInt(data.notification_count);
                $("#chat-private-id-notification-group-count-" + data.group_id).text(count)
            } else {
                $("#private-chat").append(data.bubbleView);
            }
        } else {
            if ($("#chat-modal-" + data.sender_id).length) {
                let count = $("#chat-private-id-notification-count-" + data.sender_id).text();
                count = parseInt(count) || 0 + parseInt(data.notification_count);
                $("#chat-private-id-notification-count-" + data.sender_id).text(count)
            } else {
                $("#private-chat").append(data.bubbleView);
            }
        }
        updateMessageInCard(data)
    }

    function updateMessageInCard(data) {
        if ($(".card-message").length) {
            if (data.group_id != 0) {
                console.log(data.group_id)
                if ($(".card-message").data("group_id") == data.group_id) {
                    $("#messagesModal").append(data.conversations)
                }
            } 
            else {
                if ($(".card-message").data("contact_id") == data.sender_id) {
                    $("#messagesModal").append(data.conversations)
                }
            }
        }
    }

    function addNewMessage(messageInfo) {
        if ($("#contact-list").length) {
            $("#contact-" + messageInfo.contact.sender_id).remove();
            $("#contact-list").prepend(messageInfo.contact.view);

            if ($(".card-message").length) {
                $(".card-message").remove();
            }

            $.post(
                url("/messaging/show-modal-message"), 
                {
                    _token: _token,
                    sender_id: messageInfo.sender
                },
                function (data, textStatus, jqXHR) {
                    $("#kt_content").append(data.view);
                }
            );
        }
       
        if ($("." + messageInfo.target).length > 1 || $("." + messageInfo.target2).length > 1) {
            $(messageInfo.target_master).last().append(messageInfo.item);
        }


    }
    function addSectionTask(extraData) {
        if (!$(".nav-item-section-task").length) {
            return;  
        }
        if (extraData.item.deleted == "1") {
            return $("#section-id-"+extraData.item.section_id).remove();
        }
        if (extraData.item.update == "true" ) {
           return $("#section-id-"+extraData.item.section_id).replaceWith(extraData.item.data);
        }
        
        $(".nav-item-section-task:last").before(extraData.item.data);
    }
    /** Stop pulse on click notification*/
    $("#bell-icon").on("click", function() {
        if ($("#pulse-notification").hasClass("pulse-ring")) {
            $("#pulse-notification").removeClass("pulse-ring")
        }
        $.ajax({
            url: url("/notification/set/as-read"),
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": _token,
                "notifications": $("#unread_notification").val()
            },
            success: function(result) {
                if (result.success) {
                    $("#notifications-count").text("0")
                }
            },
        });
    })
</script>
