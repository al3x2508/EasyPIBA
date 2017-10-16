var jsonPage = 'administrators';
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
	if($("#userf").val()!='') filters.filters['user'] = $("#userf").val();
	if($("#statusf").val()!='-1') filters.filters['stare'] = $("#statusf").val();
	return filters;
}
function dateEdit(date) {
	$('[name="edpermisiune[]"').each(function() {
		$(this).prop('checked', false);
	});
	$.each(date.acces, function(index, value) {
		$('[name="edpermisiune[' + value + ']"').prop('checked', true);
	});
}