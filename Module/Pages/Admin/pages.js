var jsonPage = 'Pages',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "url" },
		{ "mData": function(e) {
			return e.languages.name;
		} },
		{ "mData": "title" },
		{ "mData": "menu_text" },
		{ "mData": function(e) {
			return "<span class=\"actions btn btn-outline-primary btn-outline-primary fas fa-folder-open\" data-url=\"/" + e.url + "\"></span><span class=\"actions btn btn-outline-primary btn-outline-primary fas fa-edit\" title=\"" + jsstrings.edit + "\"></span><span class=\"actions btn btn-outline-danger fas fa-trash\" title=\"" + jsstrings.delete + "\" data-actid=\"" + e.id + "\" data-toggle=\"modal\" data-target=\"#confirm_delete\"></span>";
		}}
	],
	delAction = 'delete_page';
function loadData(aoData) {
	var filters = {};
	$(aoData).each(function(i, val) {
		if(val.name == 'sEcho') filters.secho = val.value;
		else if(val.name == 'iDisplayStart') filters.start = val.value;
		else if(val.name == 'iDisplayLength') filters.length = val.value;
	});
	filters.filters = {};
	if($("#idf").val()!='') filters.filters['id'] = $("#idf").val();
	if($("#urlf").val()!='') filters.filters['url'] = $("#urlf").val();
	if($("#languagef").val()!='0') filters.filters['language'] = $("#languagef").val();
	if($("#titlef").val()!='') filters.filters['title'] = $("#titlef").val();
	if($("#menutextf").val()!='') filters.filters['menu_text'] = $("#menutextf").val();
	return filters;
}
$(document).ready(function() {
	CKEDITOR.plugins.addExternal('imageuploader', adminfolder + 'plugins/imageuploader/');
	CKEDITOR.replace('edcontent', {
		allowedContent: true,
		extraPlugins: 'imageuploader,justify,codesnippetgeshi',
		codeSnippetGeshi_url: '/admin/plugins/codesnippetgeshi/colorize.php'
	});
	CKEDITOR.dtd.$removeEmpty['i'] = false;
	CKEDITOR.dtd.$removeEmpty['span'] = false;
	$("body").on('click', 'span.actions', function() {
		if($(this).hasClass('fa-folder-open')) window.open($(this).data('url'), '_blank');
	});
});