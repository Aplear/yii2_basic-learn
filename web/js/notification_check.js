

$.get('index.php?r=notification%2Findex', function(data){  // Use AJAX to get the notification
    //if user have permission for notification check
    if(data.status) {
        getNotification();
    }
});

// get notifications and check if set
var getNotification = function() {
    //check ones a 15 seconds
    setInterval(function(){
        $.get('index.php?r=notification%2Fcheck', function(data){  // Use AJAX to get the notification
            if(!jQuery.isEmptyObject(data.notifications)) {
                pushNotification(data);
            }
        });
    }, 15000);

    return false;
};

/*
* push alerts into DOM
* obj notifications object
* */
function pushNotification(obj)
{
    var block_notification = $(".absolute-alert-block").empty();
    var alert_block;
    var change_status_url;
    var a_close;

    $.each(obj.notifications, function (index, value) {

        change_status_url = 'index.php?r=notification%2Fchange-status&id='+value.id;

        block_notification.append("<div class='alert alert-info alert-dismissable fade in'>" +
            "<a href='#' data-link='index.php?r=notification%2Fchange-status&id="+value.id+"' class='btn btn-default mark_as_read' data-dismiss='alert' role='button'>Mark as read</a>" +
            "<strong> Info!</strong> You have a " + value.notification +
            "</div>");


    })
}
//if click marked as read
$(document).on('click', '.mark_as_read', function(e) {
    e.preventDefault();
    var url = $(this).attr("data-link");
    $.get(url, function (data) {
       console.log(2);
    });
});