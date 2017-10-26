$(function() {
	$("#edit").click(function () {
		if ($(this).data('action') == 'edit') {
			$(this).text(jsstrings.save).data('action', 'save');
			$("#viewinfo").toggle();
			$("#editinfo").toggle();
		}
		else {
			var form = $("#myaccount"),
				constraints = {
					firstname: {
						presence: true,
						format: {
							pattern: "^([\u00c0-\u01ffa-zA-Z'-]{2,})+$",
							message: jsstrings.invalid + ' ' + jsstrings.firstname
						}
					},
					lastname: {
						presence: true,
						format: {
							pattern: "^([\u00c0-\u01ffa-zA-Z'-]{2,})+$",
							message: jsstrings.invalid + ' ' + jsstrings.lastname
						}
					},
					email: {
						presence: true,
						email: {
							message: jsstrings.invalid + ' ' + jsstrings.email
						}
					},
					country: {
						presence: true,
						format: {
							pattern: "^([0-9]{1,3})$",
							message: jsstrings.invalid + ' ' + jsstrings.country
						}
					},
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
					$("#" + invalidField).closest(".input").append($("<div></div>").addClass("alert alert-danger").text(value));
				});
			}
		}
	});
});