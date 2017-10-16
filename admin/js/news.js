var jsonPage = 'stiri',
	aoColumns = [
		{ "mData": "id" },
		{ "mData": "titlu" },
		{ "mData": "name_admin" },
		{ "mData": "data" },
		{ "mData": function (e) {
			return $('#statusf option[value="' + e.stare + '"]').text();
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
	$(".autocomplete-autor").combobox({
		source: function(request, response) {
			var data = {};
			data.filters = {};
			data.filters.nume = request.term;
			$.ajax({
				type: 'POST',
				url: 'json/admins.php',
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
	if($("#titluf").val()!='') filters.filters['titlu'] = $("#titluf").val();
	if($("#autorf").data('ui-autocomplete') && $("#autorf").data('ui-autocomplete').hasOwnProperty('selectedItem') && $("#autorf").data('ui-autocomplete').selectedItem) filters.filters['admin'] = $("#autorf").data('ui-autocomplete').selectedItem.id;
	if($("#statusf").val()!='-1') filters.filters['stare'] = $("#statusf").val();
	return filters;
}