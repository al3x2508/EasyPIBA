$(function () {
    $("#register-form").hide();
    $('#login-form-link').click(function (e) {
        $("#login-form").delay(100).fadeIn(100);
        $("#register-form").fadeOut(100);
        $('#register-form-link').removeClass('active');
        $(this).addClass('active');
        e.preventDefault();
    });
    $('#register-form-link').click(function (e) {
        $("#register-form").delay(100).fadeIn(100);
        $("#login-form").fadeOut(100);
        $('#login-form-link').removeClass('active');
        $(this).addClass('active');
        e.preventDefault();
    });
    $('#resend').click(function (e) {
        var emailField = $("#email-login");
        if (emailField.val().length > 4) {
            var data = {
                action: "resend",
                email: emailField.val()
            };
            $.ajax({
                type: "POST",
                url: "json/",
                data: data,
                dataType: "json",
                success: function (data) {
                    alert(data.message);
                    if (data.hasOwnProperty('redirect')) window.location.href = data.redirect;
                }
            });
        }
        else {
            alert(jsstrings.enter_email);
            emailField.focus();
        }
        e.preventDefault();
    });
});