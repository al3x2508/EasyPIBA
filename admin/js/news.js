var jsonPage = 'news',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": function(e) {
			return e.languages.name;
		} },
		{ "mData": "title" },
		{ "mData": function(e) {
			return e.admins.name;
		} },
		{ "mData": "date_published" },
		{ "mData": function (e) {
			return $('#statusf').find('option[value="' + e.status + '"]').text();
		} },
		{ "mData": function() {
			return "<span class=\"actions btn fa fa-edit\"></span>";
		} }
	];
$(function() {
	$.widget.bridge('uitooltip', $.ui.tooltip);
	CKEDITOR.replace('edcontent', {
		allowedContent: true,
		extraPlugins: 'justify'
	});
	$(".autocomplete-author").combobox({
		source: function(request, response) {
			var data = {};
			data.filters = {};
			data.filters.name = request.term;
			$.ajax({
				type: 'POST',
				url: 'json/administrators.php',
				data: data,
				jsonp: "callback",
				dataType: "jsonp",
				success: function(data) {
					response($.map(data, function(el, index) {
						var iddata = index.substring(1);
						return {
							value: el,
							id: iddata
						};
					}));
				}
			});
		},
		minLength: 3,
		change: function() {
			$('#data_table').dataTable().fnReloadAjax();
		}
	});
	$('.select2').select2({
		width: '200px'
	}).on('select2:select', function () {
		table.fnReloadAjax();
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
	if($("#languagef").val()!='0') filters.filters['language'] = $("#languagef").val();
	if($("#titlef").val()!='') filters.filters['title'] = $("#titlef").val();
	if($("#authorf").data('ui-autocomplete') && $("#authorf").data('ui-autocomplete').hasOwnProperty('selectedItem') && $("#authorf").data('ui-autocomplete').selectedItem) filters.filters['admin'] = $("#authorf").data('ui-autocomplete').selectedItem.id;
	if($("#statusf").val()!='-1') filters.filters['status'] = $("#statusf").val();
	return filters;
}