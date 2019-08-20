var jsonPage = 'Users',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "firstname" },
		{ "mData": "lastname" },
		{ "mData": "countries.name" },
		{ "mData": "email" },
		{ "mData": function (e) {
			return $('#statusf option[value="' + e.status + '"]').text();
		} },
		{ "mData": function(e) {
			return "<span class=\"actions btn btn-outline-primary btn-outline-primary fas fa-edit\" title=\"" + jsstrings.edit + "\"></span><span class=\"actions btn btn-outline-danger fas fa-trash\" title=\"" + jsstrings.delete + "\" data-actid=\"" + e.id + "\" data-toggle=\"modal\" data-target=\"#confirm_delete\"></span>";
		} }
	],
	delAction = 'delete_user';
function loadData(aoData) {
	var filters = {};
	$(aoData).each(function(i, val) {
		if(val.name == 'sEcho') filters.secho = val.value;
		else if(val.name == 'iDisplayStart') filters.start = val.value;
		else if(val.name == 'iDisplayLength') filters.length = val.value;
	});
	filters.filters = {};
	if($("#idf").val()!='') filters.filters['id'] = $("#idf").val();
	if($("#firstnamef").val()!='') filters.filters['firstname'] = $("#firstnamef").val();
	if($("#lastnamef").val()!='') filters.filters['lastname'] = $("#lastnamef").val();
	if($("#countryf").val()!='0') filters.filters['country'] = $("#countryf").val();
	if($("#emailf").val()!='') filters.filters['email'] = $("#emailf").val();
	if($("#statusf").val()!='-1') filters.filters['status'] = $("#statusf").val();
	return filters;
}