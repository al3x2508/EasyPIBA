var jsonPage = 'Testimonials',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "name" },
		{ "mData": "company" },
		{ "mData": "short" },
		{ "mData": function (e) {
			return $('#statusf option[value="' + e.status + '"]').text();
		} },
		{ "mData": function(e) {
			return "<span class=\"actions btn btn-outline-primary fas fa-edit\" title=\"" + jsstrings.edit + "\"></span><span class=\"actions btn btn-outline-danger fas fa-trash\" title=\"" + jsstrings.delete + "\" data-actid=\"" + e.id + "\" data-toggle=\"modal\" data-target=\"#confirm_delete\"></span>";
		} }
	],
	delAction = 'delete_testimonial';
$(function() {
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
	filters.filters = {};
	if($("#idf").val()!='') filters.filters['id'] = $("#idf").val();
	if($("#namef").val()!='') filters.filters['name'] = $("#namef").val();
	if($("#statusf").val()!='-1') filters.filters['status'] = $("#statusf").val();
	return filters;
}