// AJAX PROCESS > PHP - LOGIN
$(function () {
  $("#btnLogin").click(function () {
    // check values
    $("#createBookMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();
    let urlAjax = devDirectory + "/api/auth/login";

    $.ajax({
      type: "POST",
      url: urlAjax,
      data: {
        userName: $("#username").val(),
        password: $("#password").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          localStorage.setItem('token', response.token);

          // Add response in Modal body
          $("#loginMessageOk").show();
          $("#loginMessageErr").hide();
          // redirect page
          setTimeout(function () {
            window.location = devDirectory + "/admin";
          }, 1300);
        } else {
          // show error message
          $("#loginMessageOk").hide();
          $("#loginMessageErr").show();
        }
      },
    });
  });
});
