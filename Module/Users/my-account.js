var delAction = 'delete_account',
	_confirmDelete = $('<div></div>');
$(function() {
	var _edit = $("#edit"),
		_changePwd = $("#editPassword");
	_edit.click(function () {
		if ($(this).data('action') == 'edit') {
			$(this).text(jsstrings.save).data('action', 'save');
			$('.btn-password').addClass('d-none');
			$('.btn-delete').removeClass('d-none');
			$("#viewinfo").toggle();
			$("#editinfo").toggle();
		}
		else {
			var form = $("#myaccount"),
				constraints = {
					firstname: {
						presence: true,
						format: {
							pattern: "^([\u00c0-\u01ffa-zA-Z' -]{2,})+$",
							message: jsstrings.invalid + ' ' + jsstrings.firstname
						}
					},
					lastname: {
						presence: true,
						format: {
							pattern: "^([\u00c0-\u01ffa-zA-Z' -]{2,})+$",
							message: jsstrings.invalid + ' ' + jsstrings.lastname
						}
					},
					country: {
						presence: true,
						format: {
							pattern: "^([0-9]{1,3})$",
							message: jsstrings.invalid + ' ' + jsstrings.country
						}
					}
				},
				invalidFields = validate(validate.collectFormValues(form), constraints, {fullMessages: false});
			form.find(".alert").remove();
			if(!invalidFields) form.submit();
			else {
				$.each(invalidFields, function(invalidField, value) {
					var invalidFieldInput = $("#" + invalidField);
					invalidFieldInput.closest(".input-group").append($("<div></div>").addClass("alert alert-danger").text(value));
					invalidFieldInput[0].setCustomValidity(value);
					if(invalidFieldInput.val()) invalidFieldInput.addClass("has-value");
					else invalidFieldInput.removeClass("has-value");
					invalidFieldInput.change(function() {
						$(this)[0].setCustomValidity("");
						if($(this).val()) $(this).addClass("has-value");
						else $(this).removeClass("has-value");
						invalidFieldInput.closest(".input-group").children(".alert-danger").remove();
					});
				});
			}
		}
	});
	_changePwd.click(function () {
		if ($(this).data('action') == 'password') {
			$(this).text(jsstrings.save).data('action', 'save');
			_edit.addClass('d-none');
			$('.btn-delete').addClass('d-none');
			$("#viewinfo").toggle();
			$("#editpasswordF").toggle();
		}
		else {
			var form = $("#editPwd"),
				constraints = {
					password: {
						length: {
							minimum: 8,
							message: jsstrings.minchars.replace("%i", 8)
						}
					},
					confirmPassword: {
						equality: {
							attribute: "password",
							message: jsstrings.confirmPassword
						}
					}
				},
				invalidFields = validate(validate.collectFormValues(form), constraints, {fullMessages: false});
			form.find(".alert").remove();
			if(!invalidFields) form.submit();
			else {
				$.each(invalidFields, function(invalidField, value) {
					var invalidFieldInput = $("#" + invalidField);
					invalidFieldInput.closest(".input-group").append($("<div></div>").addClass("alert alert-danger").text(value));
					invalidFieldInput[0].setCustomValidity(value);
					if(invalidFieldInput.val()) invalidFieldInput.addClass("has-value");
					else invalidFieldInput.removeClass("has-value");
					invalidFieldInput.change(function() {
						$(this)[0].setCustomValidity("");
						if($(this).val()) $(this).addClass("has-value");
						else $(this).removeClass("has-value");
						invalidFieldInput.closest(".input-group").children(".alert-danger").remove();
					});
				});
			}
		}
	});
	$("#confirm_delete .btn-ok").click(function() {
		$.ajax({
			type: "POST",
			url: "/json",
			data: {
				action: "delete-account"
			},
			dataType: "json",
			success: function (data) {
				alert(data.message);
				if (data.hasOwnProperty('redirect')) window.location.href = data.redirect;
			}
		});
	});
});
function initCompleteCallback() {
	if($("#editinfo").is(':visible')) {
		$("#edit").text(jsstrings.save).data('action', 'save');
		$('.btn-password').addClass('d-none');
		$('.btn-delete').removeClass('d-none');
	}
	else if($("#editpasswordF").is(':visible')) {
		$("#editPassword").text(jsstrings.save).data('action', 'save');
		$("#edit").addClass('d-none');
		$('.btn-password').removeClass('d-none');
		$('.btn-delete').addClass('d-none');
	}
}