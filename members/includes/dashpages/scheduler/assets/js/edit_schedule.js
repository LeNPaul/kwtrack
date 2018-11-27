// Functions to handle cookies
var createCookie = function(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else var expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
};

var readCookie = function(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
};

var getCookie = function(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) {
    return parts.pop().split(";").shift();
  } else {
    return 0;
  }
};

var eraseCookie = function(name) {
  createCookie(name,"",-1);
};

if (getCookie("l_cid") === 0) window.location.href = 'dashboard?p=as';

$(function() {

  $("#ad_schedule").on("click", function() {
    var selectedCheckboxes = $('input[type=checkbox]:checkbox:checked');

    // Create empty schedule array of size 7 x 24
    var scheduleArr = [];
    for (i = 0; i < 7; i++) { scheduleArr[i] = Array(24).fill().map((e) => 0); }

    // Format schedule array properly based on user input
    for (i = 0; i < selectedCheckboxes.length; i++) {
      var e  = selectedCheckboxes[i];
      var id = e.id.split(',');
      scheduleArr[parseInt(id[0])][parseInt(id[1])] = 1;
    }

    var o = JSON.stringify(scheduleArr);

    var l_cid = getCookie("l_cid");

    $.ajax({
     type: "POST",
     url: "includes/dashpages/scheduler/assets/api/__save_sched.php",
     data: { s:o, c:l_cid },
     success: function(data) {
       console.log(data);
       eraseCookie("l_cid");

       swal({
         title: 'Campaign Schedules Updated',
         text: 'All schedules updated for the selected campaigns.',
         type: 'success',
         showCancelButton: false,
         confirmButtonClass: "btn-success"
       })
       .then(function(result) {
         if (result.value) {
           window.location.href = 'dashboard?p=as';
         }
       });
     },
     error: function(err) {
       alert(err);
     }
    });
  });



});