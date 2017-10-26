var jsonPage = 'administrators';
function loadData(aoData) {
	var filters = {};
	$(aoData).each(function (i, val) {
		if (val.name == 'sEcho') filters.secho = val.value;
		else if (val.name == 'iDisplayStart') filters.start = val.value;
		else if (val.name == 'iDisplayLength') filters.length = val.value;
	});
	filters.filters = {};
	if ($("#idf").val() != '') filters.filters['id'] = $("#idf").val();
	if ($("#namef").val() != '') filters.filters['name'] = $("#namef").val();
	if ($("#usernamef").val() != '') filters.filters['username'] = $("#usernamef").val();
	if ($("#statusf").val() != '-1') filters.filters['status'] = $("#statusf").val();
	return filters;
}
function dateEdit(date) {
	$('[name="edpermission[]"').each(function () {
		$(this).prop('checked', false).iCheck('update');
	});
	$.each(date.access, function (index, value) {
		$('[name="edpermission[' + value + ']"').prop('checked', true).iCheck('update');
	});
}
$(function () {
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_minimal-blue',
		radioClass: 'iradio_minimal-blue'
	});
});