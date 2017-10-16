var jsonPage = 'media',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "fisier" },
		{ "mData": function(e) {
			return "<img src='/img/uploads/" + e.fisier + "' />";
		} },
		{ "mData": function() {
			return "<span class=\"actions btn fa fa-trash-o\"></span>";
		} }
	];
function loadData(aoData) {
	var filters = {};
	$(aoData).each(function(i, val) {
		if(val.name == 'sEcho') filters.secho = val.value;
		else if(val.name == 'iDisplayStart') filters.start = val.value;
		else if(val.name == 'iDisplayLength') filters.length = val.value;
	});
	filters.filters = new Object();
	if($("#idf").val()!='') filters.filters['id'] = $("#idf").val();
	return filters;
}