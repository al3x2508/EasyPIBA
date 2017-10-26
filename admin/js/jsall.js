'use strict';
var jsstrings = {},
	dataTable = false,
	table = false,
	currentPage = 0,
	saveButton;
$(function () {
	if ($("#save").length > 0) saveButton = $("#save");
	else saveButton = $("#customSave");
	$.getJSON("/js/en.json", function (data) {
		jsstrings = data;
		if (typeof jslang != 'undefined') {
			$.getJSON("/js/" + jslang + ".json", function (data) {
				$.extend(jsstrings, data);
				initComplete();
			});
		}
		else initComplete();
	});
	function initComplete() {
		if (typeof aoColumns != 'undefined') {
			dataTable = $('#data_table');
			var dataTableOptions = {
				"bPaginate": true,
				"bLengthChange": false,
				"bFilter": false,
				"bSort": false,
				"bInfo": true,
				"bAutoWidth": false,
				"bProcessing": false,
				"bServerSide": true,
				"sAjaxSource": 'json/' + jsonPage + '.php',
				"fnServerData": function (sSource, aoData, fnCallback, oSettings) {
					oSettings.jqXHR = $.ajax({
						"dataType": 'json',
						"type": "POST",
						"url": sSource,
						"data": loadData(aoData),
						"success": fnCallback
					})
				},
				"aoColumns": aoColumns
			};
			if (typeof jslang != 'undefined') dataTableOptions.oLanguage = jsstrings.datatables;
			table = dataTable.dataTable(dataTableOptions);
			dataTable.on('page.dt', function () {
				var info = dataTable.DataTable().page.info();
				currentPage = info.page;
			});
		}
	}

	$("body").on('click', 'span.fa-trash-o', function (event) {
		var v = confirm(jsstrings.confirm_delete);
		if (v == false) {
			if (event.stopPropagation) {
				event.stopPropagation();
				event.preventDefault();
			}
			else if (window.event) return false;
		}
		else {
			if (typeof jsonPage != 'undefined') {
				var deleteId = ($(event.target).data('actid')) ? $(event.target).data('actid') : $(event.target).closest('tr').children('td').eq(0).text();
				$.post("act/" + jsonPage + ".php", {delete: deleteId}, function () {
					table.fnReloadAjax();
				});
			}
		}
	}).on('click', '#add', function () {
		if (typeof CKEDITOR != 'undefined') CKEDITOR.instances.edcontent.setData('');
		$("#edtable").find(":input").each(function () {
			var name = $(this).attr("name");
			if (name && name.substring(0, 2) == "ed") {
				if (name != 'edimage') $(this).val('');
				if ($(this).data('ui-autocomplete')) {
					if (!$(this).data('ui-autocomplete').hasOwnProperty('selectedItem') || !$(this).data('ui-autocomplete').selectedItem) $(this).data('ui-autocomplete').selectedItem = {id: false};
					else $(this).data('ui-autocomplete').selectedItem.id = false;
				}
			}
		});
		saveButton.data("actid", 0).closest('.modal').show().find('.modal-title').eq(0).text(jsstrings.buttonAdd);
		$('#imagePreview').attr('src', '/img/news/default-360x220.jpg');
		$('#edimage').data('imgname', '');
	}).on('click', '.btn-export', function () {
		var date = loadData(),
			url = 'json/' + jsonPage + '.php',
			inputs = '';
		date.secho = 1;
		date.export = $(this).text().toLowerCase();
		$.each(date, function (key, value) {
			if (typeof value === 'object') {
				$.each(value, function (key2, value2) {
					if (typeof value2 !== 'object') inputs += '<input type="hidden" name="' + key + '[' + key2 + ']" value="' + value2 + '" />';
					else {
						$.each(value2, function (key3, value3) {
							inputs += '<input type="hidden" name="' + key + '[' + key2 + '][]" value="' + value3 + '" />';
						});
					}
				});
			}
			else inputs += '<input type="hidden" name="' + key + '" value="' + value + '" />';
		});
		$('<form action="' + url + '" method="post">' + inputs + '</form>').appendTo('body').submit().remove();
	}).on('click', 'span.actions', function () {
		var actid = $(this).data('actid') ? $(this).data('actid') : $(this).closest('tr').children('td').eq(0).text();
		if ($(this).hasClass('fa-edit')) {
			$("#edtable").find("input").each(function () {
				if ($(this).attr('type') != 'checkbox') $(this).val('');
				else $(this).prop('checked', false);
			});
			var dataPost = {};
			if ($(this).data('col')) {
				dataPost.filters = {};
				dataPost.filters[$(this).data('col')] = $(this).data('cid');
				actid = $(this).data('cid');
			}
			else dataPost.id = actid;
			$.ajax({
				url: 'json/' + jsonPage + '.php',
				type: 'POST',
				data: dataPost,
				dataType: 'json',
				success: function (data) {
					var editData = false;
					$.each(data, function (key, value) {
						if (key !== 'aaData' && typeof value === 'object' && value !== null) {
							if (!editData) editData = [];
							editData[key] = value;
						}
						else {
							if (key != 'content') {
								var elm = $("#ed" + key);
								if (elm.is(':checkbox')) {
									if (value == 1) elm.prop('checked', 'checked');
								}
								else if (!elm.is(':file')) elm.val(value);
								else {
									$('#imagePreview').attr('src', '/img/news/' + value);
									$('#edimage').data('imgname', '');
								}
								if (key.indexOf('name_') === 0 && elm.hasClass('ui-autocomplete-input')) {
									var dtId = key.replace('name_', '');
									if (!elm.data('ui-autocomplete').hasOwnProperty('selectedItem') || !elm.data('ui-autocomplete').selectedItem) elm.data('ui-autocomplete').selectedItem = {};
									if (!elm.data('ui-autocomplete').selectedItem) elm.data('ui-autocomplete').selectedItem.id = false;
									elm.combobox().data('ui-autocomplete').selectedItem.id = data[dtId];
								}
							}
							else CKEDITOR.instances.edcontent.setData(value);
						}
					});
					if (editData && typeof dateEdit == 'function') dateEdit(editData);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(thrownError);
				}
			});
			saveButton.data("actid", actid).closest('.modal').show().find('.modal-title').eq(0).text(jsstrings.buttonEdit);
		}
	});
	$(window).on('keyup', function (e) {
		if (e.which == 27 && $('.modal:visible').length > 0) {
			saveButton.data("actid", 0);
			$('.modal').hide();
		}
	});
	$('[data-dismiss="modal"]').click(function () {
		var mdldlg = $(this).closest('.modal'),
			sc = $("#customsave");
		mdldlg.hide();
		if (!sc.length || sc.data("actid") == 0) {
			var eimg = mdldlg.find("#edimage");
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
	$("#save").click(function () {
		var data = {},
			mdl = $(this).closest('.modal');
		data.id = $(this).data("actid");
		$("#edtable").find(":input").each(function () {
			var fieldName = $(this).attr("name");
			if (fieldName && fieldName.substring(0, 2) == "ed") {
				fieldName = fieldName.substring(2);
				if (fieldName == 'content') data.content = CKEDITOR.instances.edcontent.getData();
				else if (fieldName == 'image') data[fieldName] = $(this).data('imgname');
				else {
					if ($(this).data('ui-autocomplete') && $(this).data('ui-autocomplete').hasOwnProperty('selectedItem') && $(this).data('ui-autocomplete').selectedItem) {
						fieldName = fieldName.replace('name_', '');
						data[fieldName] = $(this).data('ui-autocomplete').selectedItem.id;
					}
					else {
						if ($(this).is(':checkbox')) data[fieldName] = ($(this).is(':checked')) ? 1 : 0;
						else data[fieldName] = $(this).val();
					}
				}
			}
		});
		$.ajax({
			url: 'act/' + jsonPage + '.php',
			type: 'POST',
			data: data,
			dataType: 'text',
			success: function () {
				table.fnReloadAjax();
				mdl.hide();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log(thrownError);
			}
		});
	});
	$('#imagePreview').click(function () {
		$("#edimage").trigger('click');
	});
	$("#edimage").on("change", function () {
		var formData = new FormData(),
			eimg = $(this),
			iprev = $("#imagePreview");
		formData.append('edimage', eimg[0].files[0]);
		$.ajax({
			type: 'POST',
			url: 'act/uploadimg.php',
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (data) {
				if (data.src) {
					iprev.attr('src', data.src);
					eimg.data('imgname', data.image);
				}
				else alert(data.error);
			}
		});
	});
	$(".tableFilter").change(function () {
		table.fnReloadAjax();
	});
});