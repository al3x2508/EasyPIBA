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
			return "<span class=\"actions btn btn-sm btn-outline-primary fal fa-folder-open\" data-url=\"" + folder + e.url + "\"></span><span class=\"actions btn btn-sm btn-outline-primary fal fa-edit\" title=\"" + jsstrings.edit + "\"></span><span class=\"actions btn btn-sm btn-outline-danger fal fa-trash\" title=\"" + jsstrings.delete + "\" data-actid=\"" + e.id + "\" data-toggle=\"modal\" data-target=\"#confirm_delete\"></span>";
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
	CKEDITOR.plugins.add('maincss', {
		beforeInit: function( editor ) {
			editor.addContentsCss( folder + 'css/main.css' );
		}
	});
	CKEDITOR.replace('edcontent', {
		allowedContent: true,
		extraPlugins: 'imageuploader,justify,maincss',
		fillEmptyBlocks: false,
		height: 560
	});
	CKEDITOR.dtd.$removeEmpty['i'] = false;
	CKEDITOR.dtd.$removeEmpty['span'] = false;
	$("body").on('click', 'span.actions', function() {
		if($(this).hasClass('fa-folder-open')) window.open($(this).data('url'), '_blank');
	});
});