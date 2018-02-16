'use strict';
var jsstrings = {},
	dataTable = false,
	table = false,
	currentPage = 0,
	saveButton,
	folder = '',
	adminfolder = '',
	_confirmDelete = $('<div></div>'),
	_messagePopup = $('<div></div>');
$(function () {
	folder = $("#mainjs").data('appdir');
	adminfolder = $("#mainjs").data('admindir');
	$(".sidebar-toggle").click(function() {
		if($(window).width() > 767) $('body').toggleClass('sidebar-collapse');
		else $('body').toggleClass('sidebar-open');
	});
	if ($("#save").length > 0) saveButton = $("#save");
	else saveButton = $("#customSave");
	$.getJSON(folder + "js/en.json", function (data) {
		jsstrings = data;
		if (typeof jslang != 'undefined') {
			$.getJSON(folder + "js/" + jslang + ".json", function (data) {
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
				"sAjaxSource": adminfolder + 'json/' + jsonPage,
				"fnServerData": function (sSource, aoData, fnCallback, oSettings) {
					oSettings.jqXHR = $.ajax({
						"dataType": 'json',
						"type": "POST",
						"url": sSource,
						"data": loadData(aoData),
						"success": fnCallback
					})
				},
				"aoColumns": aoColumns,
				"responsive": {
					"details": {
						"display": $.fn.dataTable.Responsive.display.childRowImmediate,
						"type": ''
					}
				}
			};
			if (typeof jslang != 'undefined') dataTableOptions.oLanguage = jsstrings.datatables;
			if(typeof dtSort != 'undefined') {
				dataTableOptions.order = dtSort;
				dataTableOptions.bSort = true;
			}
			if(typeof aoColumnDefs != 'undefined') {
				dataTableOptions.aoColumnDefs = aoColumnDefs;
			}
			table = dataTable.DataTable(dataTableOptions);
			table.on('page.dt', function () {
				var info = table.page.info();
				currentPage = info.page;
			});
			if (typeof responsiveResize != 'undefined') table.on('responsive-resize', function () {
				responsiveResize();
			});
			$.fn.dataTable.ext.errMode = 'none';

			if(typeof delAction !== 'undefined') {
				delAction = jsstrings[delAction];
				_confirmDelete.addClass('modal fade').attr('id', 'confirm_delete').attr('role', 'dialog').attr('aria-hidden', 'true').on('show.bs.modal', function (e) {
					$(this).find('.btn-ok').data('actid', $(e.relatedTarget));
				});
				_confirmDelete.append($('<div></div>').addClass('modal-dialog').append(
					$('<div></div>').addClass('modal-content').append(
						$('<div></div>').addClass('modal-header').append($('<button></button>').addClass('close').attr('data-dismiss', 'modal').attr('aria-hidden', 'true').html('&times;')).append($('<h4></h4>').addClass('modal-title').attr('id', 'myModalLabel').text(jsstrings.confirm_delete))
					)
						.append(
							$('<div></div>').addClass('modal-body').append($('<p></p>').text(delAction)).append($('<p></p>').text(jsstrings.confirm_proceed))
						)
						.append(
							$('<div></div>').addClass('modal-footer').append($('<button></button>').addClass('btn btn-default').attr('data-dismiss', 'modal').text(jsstrings.cancel)).append($('<button></button>').addClass('btn btn-danger btn-ok').text(jsstrings.delete).click(function () {
									proceedDelete($(this).data('actid'));
								})
							)
						)));
				$('body').append(_confirmDelete);
			}
			_messagePopup.addClass('modal fade').attr('role', 'dialog').attr('aria-hidden', 'true')
				.append($('<div></div>').addClass('modal-dialog').append(
					$('<div></div>').addClass('modal-content').append(
						$('<div></div>').addClass('modal-header').append($('<button></button>').addClass('close').attr('data-dismiss', 'modal').attr('aria-hidden', 'true').html('&times;')).append($('<h4></h4>').addClass('modal-title'))
					)
						.append(
							$('<div></div>').addClass('modal-body').append($('<p></p>'))
						)
						.append(
							$('<div></div>').addClass('modal-footer').append($('<button></button>').addClass('btn btn-ok').attr('data-dismiss', 'modal').text(jsstrings.ok)
							)
						)));
			$('body').append(_messagePopup);
		}
	}
	$(".drpicker").daterangepicker({
		autoUpdateInput: false,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
			'Always': ['', '']
		}
	}).on('apply.daterangepicker', function(ev, picker) {
		if(picker.chosenLabel == 'Always') {
			$(this).val(picker.chosenLabel);
			if($(this).closest('#data_table').length) datatableAjaxReload();
		}
		else {
			$(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
			if($(this).closest('#data_table').length) datatableAjaxReload();
		}
	}).on('cancel.daterangepicker', function() {
		$(this).val('');
		if($(this).closest('#data_table').length) datatableAjaxReload();
	});
	$("body").on('click', '#add', function () {
		if (typeof CKEDITOR != 'undefined') CKEDITOR.instances.edcontent.setData('');
		$("#edtable").find(":input").each(function () {
			var name = $(this).attr("name");
			if (name && name.substring(0, 2) == "ed") {
				if (name != 'edimage') {
					if($(this).children('option[selected]')) $(this).val($(this).children('option[selected]').val());
					else {
						if($(this).attr('value')) $(this).val($(this).attr('value'));
						else $(this).val('');
					}
				}
				if ($(this).data('ui-autocomplete')) {
					if (!$(this).data('ui-autocomplete').hasOwnProperty('selectedItem') || !$(this).data('ui-autocomplete').selectedItem) $(this).data('ui-autocomplete').selectedItem = {id: false};
					else $(this).data('ui-autocomplete').selectedItem.id = false;
				}
			}
		});
		saveButton.data("actid", 0).closest('.modal').modal('show').find('.modal-title').eq(0).text(jsstrings.buttonAdd);
		$('#imagePreview').attr('src', folder + 'img/' + $('#imagePreview').data('folder') + '/' + $('#imagePreview').data('default'));
		$('#edimage').data('imgname', '');
	}).on('click', '.btn-export', function () {
		var date = loadData(),
			url = adminfolder + 'json/' + jsonPage,
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
				if ($(this).attr('type') != 'checkbox') {
					if($(this).attr('value')) $(this).val($(this).attr('value'));
					else $(this).val('');
				}
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
				url: adminfolder + 'json/' + jsonPage,
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
									if(value) $('#imagePreview').attr('src', folder + 'img/' + $('#imagePreview').data('folder') + '/' + value);
									$('#edimage').data('imgname', '');
								}
								if (elm.hasClass('ui-autocomplete-input')) {
									if (!elm.data('ui-autocomplete').hasOwnProperty('selectedItem') || !elm.data('ui-autocomplete').selectedItem) elm.data('ui-autocomplete').selectedItem = {};
									if (!elm.data('ui-autocomplete').selectedItem) elm.data('ui-autocomplete').selectedItem.id = false;
									elm.combobox().data('ui-autocomplete').selectedItem.id = data[key];
									elm.val(data[data['schema'][key]['table_reference']]['name']);
								}
								else if (elm.hasClass('datetimepicker')) {
									elm.data("DateTimePicker").date(value);
								}
							}
							else CKEDITOR.instances.edcontent.setData(value);
						}
					});
					if (editData && typeof dataEdit == 'function') dataEdit(editData);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(thrownError);
				}
			});
			saveButton.data("actid", actid).closest('.modal').modal('show').find('.modal-title').eq(0).text(jsstrings.buttonEdit);
		}
	});
	$(window).on('keyup', function (e) {
		if (e.which == 27 && $('.modal:visible').length > 0) {
			$(".remove-pic").each(function() {
				var filename = $(this).data('src');
				$.post(adminfolder + 'act/Media', {clearImg: filename});
			});
			saveButton.data("actid", 0);
			$('.modal, .modal-backdrop').modal('hide');
		}
	});
	$('[data-dismiss="modal"]').click(function () {
		var mdldlg = $(this).closest('.modal'),
			sc = $("#customsave");
		mdldlg.modal('hide');
		if (!sc.length || sc.data("actid") == 0) {
			var eimg = mdldlg.find("#edimage");
			if (eimg.length > 0 && eimg.data('imgname') != '') $.post(adminfolder + 'act/Media', {clearImg: eimg.data('imgname')});
			$(".remove-pic").each(function() {
				var filename = $(this).data('src');
				$.post(adminfolder + 'act/Media', {clearImg: filename});
			});
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
					var isArray = false;
					if(fieldName.substr(-2) == '[]') {
						isArray = true;
						fieldName = fieldName.replace("[]", "");
						if(typeof data[fieldName] === 'undefined') data[fieldName] = [];
					}
					if ($(this).data('ui-autocomplete') && $(this).data('ui-autocomplete').hasOwnProperty('selectedItem') && $(this).data('ui-autocomplete').selectedItem) {
						if(!isArray) data[fieldName] = $(this).data('ui-autocomplete').selectedItem.id;
						else data[fieldName].push($(this).data('ui-autocomplete').selectedItem.id);
					}
					else {
						if($(this).data('value')) {
							if(!isArray) data[fieldName] = $(this).data('value');
							else data[fieldName].push($(this).data('value'));
						}
						else {
							if ($(this).is(':checkbox')) data[fieldName] = ($(this).is(':checked')) ? 1 : 0;
							else {
								if(!isArray) data[fieldName] = $(this).val();
								else data[fieldName].push($(this).val());
							}
						}
					}
				}
			}
		});
		if($(".remove-pic").length > 0) {
			data.pictures = [];
			$(".remove-pic").each(function () {
				data.pictures.push($(this).data('src'));
			});
		}
		$.ajax({
			url: adminfolder + 'act/' + jsonPage,
			type: 'POST',
			data: data,
			dataType: 'text',
			success: function (data) {
				if(data != 1 && data != '') {
					try {
						var json = JSON.parse(data);
						if (typeof(json.type) !== 'undefined') displayMessage(json.type, json.message);
						else if (typeof(json.message) !== 'undefined') displayMessage('warning', json.message);
						else if (typeof(json.error) === 'object') {
							$.each(json.error, function (key, value) {
								var elm = $("#ed" + key),
									frmg = elm.closest('.form-group'),
									spn = $("<span></span>"),
									errText = jsstrings[value];
								spn.addClass('help-block').text(errText);
								if(frmg.find('.help-block').length == 0) frmg.addClass('has-error').append(spn);
								elm.change(function() {
									frmg.removeClass('has-error');
									spn.remove();
								});
							});
						}
					}
					catch(e) {
						displayMessage('warning', data);
					}
				}
				else {
					datatableAjaxReload();
					mdl.modal('hide');
				}
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
			url: adminfolder + 'act/Media',
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
		datatableAjaxReload();
	});
});
function datatableAjaxReload() {
	if(typeof table.api !== 'undefined') table.api().ajax.reload();
	else table.ajax.reload();
}
function proceedDelete(e) {
	if (typeof jsonPage != 'undefined') {
		var deleteId = (e.data('actid'))?e.data('actid'):e.closest('tr').children('td').eq(0).text();
		$.post(adminfolder + "act/" + jsonPage, {delete: deleteId}, function () {
			datatableAjaxReload();
			$('.modal, .modal-backdrop').modal('hide');
		});
	}
}
function displayMessage(type, message) {
	var mh = _messagePopup.find('.modal-header').eq(0),
		mtxt = _messagePopup.find('.modal-body').eq(0).find('p').eq(0);
	mh.removeClass('alert-success alert-danger alert-warning alert-info').addClass('alert-' + type).find('h4').eq(0).text(type.replace(/(\b\w)/gi,function(m){return m.toUpperCase();}));
	mtxt.html(message);
	_messagePopup.modal('show');
}