var jsonPage = 'Media',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": function (e) {
			var re = /(?:\.([^.]+))?$/;
			var ext = re.exec(e.filename)[1];
			if(ext == 'png' || ext == 'jpg' || ext == 'gif') return "<a href=\"/img/uploads/" + e.filename + "\" target=\"_blank\">" + e.filename + "</a>";
			else return "<a href=\"/uploads/" + e.filename + "\" target=\"_blank\">" + e.filename + "</a>";
		} },
		{ "mData": function(e) {
			var re = /(?:\.([^.]+))?$/;
			var ext = re.exec(e.filename)[1];
			if(ext == 'png' || ext == 'jpg' || ext == 'gif') return "<img src='/img/uploads/" + e.filename + "' />";
			else return "<i class=\"fal fa-file\"></i>";
		} },
		{ "mData": function(e) {
			return "<span class=\"actions btn btn-primary btn-danger fal fa-trash\" href=\"#\" data-actid=\"" + e.id + "\" data-toggle=\"modal\" data-target=\"#confirm_delete\"></a>";
		} }
	],
	delAction = 'delete_media';
function loadData(aoData) {
	var filters = {};
	$(aoData).each(function(i, val) {
		if(val.name == 'sEcho') filters.secho = val.value;
		else if(val.name == 'iDisplayStart') filters.start = val.value;
		else if(val.name == 'iDisplayLength') filters.length = val.value;
	});
	filters.filters = {};
	if($("#idf").val()!='') filters.filters['id'] = $("#idf").val();
	return filters;
}