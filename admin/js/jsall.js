'use strict';
var paginaCurenta = 0;
$(function() {
	$("body").on('click', 'span.fa-trash-o', function(event) {
		var v = confirm("Ești sigur că vrei să ștergi?");
		if (v == false) {
			if (event.stopPropagation) {
				event.stopPropagation();
				event.preventDefault();
			}
			else if (window.event) return false;
		}
		else {
			if(typeof jsonPage != 'undefined') {
				var stergeId = ($(event.target).data('actid'))?$(event.target).data('actid'):$(event.target).closest('tr').children('td').eq(0).text();
				$.post("act/" + jsonPage + ".php", {sterge: stergeId}, function() {
					tabel.fnReloadAjax();
				});
			}
		}
	});
	if(typeof aoColumns != 'undefined') {
		var tabel = $('#tabel_date').dataTable({
			"bPaginate": true,
			"bLengthChange": false,
			"bFilter": false,
			"bSort": false,
			"bInfo": true,
			"bAutoWidth": false,
			"bProcessing": false,
			"bServerSide": true,
			"oLanguage": {
				"oPaginate": {
					"sFirst": "Primul",
					"sLast": "Ultimul",
					"sNext": "&Icirc;nainte",
					"sPrevious": "&Icirc;napoi"
				},
				"sEmptyTable": "F&#259;r&#259; &icirc;nregistr&#259;ri",
				"sInfo": "Afi&#537;&#259;m de la _START_ la _END_ din _TOTAL_ &icirc;nregistr&#259;ri",
				"sInfoEmpty": "Afi&#537;&#259;m 0 &icirc;nregistr&#259;ri",
				"sInfoFiltered": "(filtrate din _MAX_ &icirc;nregistr&#259;ri totale)",
				"sLoadingRecords": "&Icirc;nc&#259;rcare...",
				"sProcessing": "Procesare...",
				"sZeroRecords": "Nici o &icirc;nregistrare g&#259;sit&#259;"
			},
			"sAjaxSource": 'json/' + jsonPage + '.php',
			"fnServerData": function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					"dataType": 'json',
					"type": "POST",
					"url": sSource,
					"data": incarcaDate(aoData),
					"success": fnCallback
				})
			},
			"aoColumns": aoColumns
		});
		$('#tabel_date').on('page.dt', function () {
			var info = $('#tabel_date').DataTable().page.info();
			paginaCurenta = info.page;
		});
	}
	$(window).on('keyup', function(e) {
		if(e.which == 27 && $('.modal:visible').length > 0) {
			$("#salveaza").data("actid", 0);
			$('.modal').hide();
		}
	});
	$('[data-dismiss="modal"]').click(function() {
		var mdldlg = $(this).closest('.modal'),
			sc = $("#salveazacustom");
		mdldlg.hide();
		if(!sc.length || sc.data("actid") == 0) {
			var eimg = mdldlg.find("#edimagine");
			if (eimg.length > 0 && eimg.data('imgname') != '') $.post('act/uploadimg.php', {clearImg: eimg.data('imgname')});
			var fimghid = mdldlg.find('.fimghid');
			if (fimghid.length > 0) {
				fimghid.each(function () {
					if ($(this).data('imgname') != '') {
						var dataPost = {clearImg: $(this).data('imgname')};
						if ($(this).data('targetdir') != '') dataPost.targetdir = $(this).data('targetdir');
						$.post('act/uploadimg.php', dataPost);
					}
				});
			}
		}
	});
	$("#salveaza").click(function() {
		var data = {},
			mdl = $(this).closest('.modal');
		data.id = $(this).data("actid");
		$("#edtabel").find(":input").each(function() {
			var nume = $(this).attr("name");
			if(nume && nume.substring(0, 2) == "ed") {
				nume = nume.substring(2);
				if(nume == 'continut') data.continut = CKEDITOR.instances.edcontinut.getData();
				else if(nume == 'imagine') data[nume] = $(this).data('imgname');
				else {
					if ($(this).data('ui-autocomplete') && $(this).data('ui-autocomplete').hasOwnProperty('selectedItem') && $(this).data('ui-autocomplete').selectedItem) {
						nume = nume.replace('nume_', '');
						data[nume] = $(this).data('ui-autocomplete').selectedItem.id;
					}
					else {
						if($(this).is(':checkbox')) data[nume] = ($(this).is(':checked'))?1:0;
						else data[nume] = $(this).val();
					}
				}
			}
		});
		$.ajax({
			url: 'act/' + jsonPage + '.php',
			type: 'POST',
			data: data,
			dataType: 'text',
			success: function(data) {
				tabel.fnReloadAjax();
				mdl.hide();
			},
			error:function (xhr, ajaxOptions, thrownError){
				console.log(thrownError);
			}
		});
	});
	$('#imagePreview').click(function() {
		$("#edimagine").click();
	});
	$("#edimagine").on("change", function() {
		var formData = new FormData(),
			eimg = $(this),
			iprev = $("#imagePreview");
		formData.append('edimagine', eimg[0].files[0]);
		$.ajax({
			type: 'POST',
			url: 'act/uploadimg.php',
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			success:function(data) {
				if(data.src) {
					iprev.attr('src', data.src);
					eimg.data('imgname', data.imagine);
				}
				else alert(data.error);
			}
		});
	});
	$("body").on('click', '#adaugare', function() {
		if(typeof CKEDITOR != 'undefined') CKEDITOR.instances.edcontinut.setData('');
		$("#edtabel").find(":input").each(function() {
			var nume = $(this).attr("name");
			if(nume && nume.substring(0, 2) == "ed") {
				if(nume != 'edimagine') $(this).val('');
				if($(this).data('ui-autocomplete')) {
					if (!$(this).data('ui-autocomplete').hasOwnProperty('selectedItem') || !$(this).data('ui-autocomplete').selectedItem) $(this).data('ui-autocomplete').selectedItem = {id: false};
					else $(this).data('ui-autocomplete').selectedItem.id = false;
				}
			}
		});
		$("#salveaza").data("actid", 0).closest('.modal').show().find('.modal-title').eq(0).text('Adăugare');
		$('#imagePreview').attr('src', '/img/stiri/lansare_mykoolio-360x220.jpg');
		$('#edimagine').data('imgname', '');
	});
	$("body").on('click', '.btn-export', function() {
		var date = incarcaDate(),
			url = 'json/' + jsonPage + '.php',
			inputs = '';
		date.secho = 1;
		date.export = $(this).text().toLowerCase();
		$.each(date, function(key, value) {
			if(typeof value === 'object') {
				$.each(value, function(key2, value2) {
					if(typeof value2 !== 'object') inputs += '<input type="hidden" name="'+ key + '[' + key2 + ']" value="'+ value2 +'" />';
					else {
						$.each(value2, function(key3, value3) {
							inputs += '<input type="hidden" name="'+ key + '[' + key2 + '][]" value="'+ value3 +'" />';
						});
					}
				});
			}
			else inputs += '<input type="hidden" name="'+ key +'" value="'+ value +'" />';
		});
		$('<form action="'+ url +'" method="post">'+inputs+'</form>').appendTo('body').submit().remove();
	});
	$("body").on('click', 'span.actiuni', function() {
		var actid=$(this).data('actid')?$(this).data('actid'):$(this).closest('tr').children('td').eq(0).text();
		if($(this).hasClass('fa-edit')) {
			$("#edtabel input[type='text']").val('');
			$("#edtabel input[type='password']").val('');
			$("#edtabel input[type='email']").val('');
			$("#edtabel input[type='tel']").val('');
			$("#edtabel input[type='number']").val('');
			$("#edtabel input[type='checkbox']").prop('checked', false);
			var dataPost = {};
			if($(this).data('col')) {
				dataPost.filtre = {};
				dataPost.filtre[$(this).data('col')] = $(this).data('cid');
				actid = $(this).data('cid');
			}
			else dataPost.id = actid;
			$.ajax({
				url: 'json/' + jsonPage + '.php',
				type: 'POST',
				data: dataPost,
				dataType: 'json',
				success: function(data) {
					var dateEditare = false;
					$.each(data, function(key, value) {
						if(key !== 'aaData' && typeof value === 'object' && value !== null) {
							if(!dateEditare) dateEditare = [];
							dateEditare[key] = value;
						}
						else {
							if (key != 'continut') {
								var elm = $("#ed" + key);
								if (elm.is(':checkbox')) {
									if (value == 1) elm.prop('checked', 'checked');
								}
								else if (!elm.is(':file')) elm.val(value);
								else {
									$('#imagePreview').attr('src', '/img/stiri/' + value);
									$('#edimagine').data('imgname', '');
								}
								if (key.indexOf('nume_') === 0 && elm.hasClass('ui-autocomplete-input')) {
									var dtId = key.replace('nume_', '');
									if (!elm.data('ui-autocomplete').hasOwnProperty('selectedItem') || !elm.data('ui-autocomplete').selectedItem) elm.data('ui-autocomplete').selectedItem = {};
									if (!elm.data('ui-autocomplete').selectedItem) elm.data('ui-autocomplete').selectedItem.id = false;
									elm.combobox().data('ui-autocomplete').selectedItem.id = data[dtId];
								}
							}
							else CKEDITOR.instances.edcontinut.setData(value);
						}
					});
					if(dateEditare) dateEdit(dateEditare);
				},
				error:function (xhr, ajaxOptions, thrownError){
					console.log(thrownError);
				}
			});
			if($("#salveaza").length > 0) $("#salveaza").data("actid", actid).closest('.modal').show().find('.modal-title').eq(0).text('Editare');
			else $("#salveazacustom").data("actid", actid).closest('.modal').show().find('.modal-title').eq(0).text('Editare');
		}
	});
	$(".filtruTabel").change(function() {
		tabel.fnReloadAjax();
	});
});
