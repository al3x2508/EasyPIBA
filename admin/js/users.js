var jsonPage = 'users',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "firstname" },
		{ "mData": "lastname" },
		{ "mData": "countries.name" },
		{ "mData": "email" },
		{ "mData": function (e) {
			return $('#statusf option[value="' + e.status + '"]').text();
		} },
		{ "mData": function() {
			return "<span class=\"actions btn fa fa-edit\"></span><span class=\"actions btn fa fa-trash-o\"></span>";
		} }
	];
function loadData(aoData) {
	var filters = {};
	$(aoData).each(function(i, val) {
		if(val.name == 'sEcho') filters.secho = val.value;
		else if(val.name == 'iDisplayStart') filters.start = val.value;
		else if(val.name == 'iDisplayLength') filters.lungime = val.value;
	});
	filters.filters = {};
	if($("#idf").val()!='') filters.filters['id'] = $("#idf").val();
	if($("#firstnamef").val()!='') filters.filters['firstname'] = $("#firstnamef").val();
	if($("#lastnamef").val()!='') filters.filters['lastname'] = $("#lastnamef").val();
	if($("#countryf").val()!='0') filters.filters['country'] = $("#countryf").val();
	if($("#emailf").val()!='') filters.filters['email'] = $("#emailf").val();
	if($("#statusf").val()!='-1') filters.filters['stare'] = $("#statusf").val();
	return filters;
}