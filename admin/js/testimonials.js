var jsonPage = 'testimonials',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "name" },
		{ "mData": "company" },
		{ "mData": "short" },
		{ "mData": function (e) {
			return $('#statusf option[value="' + e.status + '"]').text();
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
	if($("#namef").val()!='') filters.filters['name'] = $("#namef").val();
	if($("#statusf").val()!='-1') filters.filters['status'] = $("#statusf").val();
	return filters;
}