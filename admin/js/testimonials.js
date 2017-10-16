var jsonPage = 'testimoniale',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "nume" },
		{ "mData": "calitate" },
		{ "mData": "short" },
		{ "mData": function (e) {
			return $('#staref option[value="' + e.stare + '"]').text();
		} },
		{ "mData": function() {
			return "<span class=\"actions btn fa fa-edit\"></span>";
		} }
	];
$(function() {
	$.widget.bridge('uitooltip', $.ui.tooltip);
	CKEDITOR.replace('edcontent', {
		allowedContent: true
	});
});
function loadData(aoData) {
	var filters = {};
	$(aoData).each(function(i, val) {
		if(val.name == 'sEcho') filters.secho = val.value;
		else if(val.name == 'iDisplayStart') filters.start = val.value;
		else if(val.name == 'iDisplayLength') filters.length = val.value;
	});
	filters.filters = new Object();
	if($("#idf").val()!='') filters.filters['id'] = $("#idf").val();
	if($("#numef").val()!='') filters.filters['nume'] = $("#numef").val();
	if($("#staref").val()!='-1') filters.filters['stare'] = $("#staref").val();
	return filters;
}