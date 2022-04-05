var jsonPage = 'Administrators',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "name" },
		{ "mData": "username" },
		{ "mData": function (e) {
			return $('#statusf option[value="' + e.status + '"]').text();
		} },
		{ "mData": function() {
			return "<span class=\"actions btn btn-outline-primary fa fa-edit\" title=\"" + jsstrings.edit + "\"></span>";
		} }
	];
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
function dataEdit(data) {
	$('[name="edpermission[]"]').each(function () {
		$(this).prop('checked', false);
	});
	$.each(data.access, function (index, value) {
		$('[name="edpermission[' + value.permission + ']"]').prop('checked', true);
	});
}