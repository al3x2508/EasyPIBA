'use strict';
let jsstrings = {},
	dataTable = false,
	table = false,
	currentPage = 0,
	saveButton,
	folder = '',
	adminfolder = '',
	body = $('body'),
	imagePreview = $('#imagePreview'),
	removePic = $(".remove-pic"),
	removeFile = $(".remove-file"),
	galleryModal = $("#galleryModal"),
	_confirmDelete = $('<div></div>'),
	_messagePopup = $('<div></div>'),
	jslang;
const eventEdit = new CustomEvent('dataEdit', {
	bubbles: true
});
$(function () {
	jslang = document.documentElement.lang;
	let mainjs_el = $("#mainjs"),
		save_button_el = $("#save");
	folder = mainjs_el.data('appdir');
	adminfolder = mainjs_el.data('admindir');
	if ($('[data-filtertype]').length) {
		$("#data_table thead tr").clone(true).appendTo('#data_table thead');
		$('#data_table thead tr:eq(1) th').each(function () {
			let el = false;
			if ($(this).data('filtertype')) {
				let divFC = $("<div></div>").addClass("form-group"),
					divIG = $("<div></div>").addClass("input-group"),
					labl = $("<label></label>").addClass("control-label");
				if ($(this).data('filtertype') === 'text') {
					el = $('<input />');
					el.attr('type', 'text');
					labl.text($(this).text());
					if ($(this).data('autocomplete')) el.addClass('autocomplete-' + $(this).data('autocomplete')).attr('autocomplete', 'nope');
					if ($(this).data('value')) el.attr('value', $(this).data('value'));
					if ($(this).data('disabled')) el.attr('disabled', 'true');
				} else if ($(this).data('filtertype') === 'select') {
					el = $('<select></select>');
					let opt = $(this).data('options');
					Object.keys(opt).sort(function (a, b) {
						return a - b
					}).forEach(function (key) {
						let option = $("<option></option>");
						option.val(key);
						if (typeof opt[key] === 'object') {
							option.text(opt[key][0]);
							if (opt[key][1]) option.attr('selected', 'selected');
						} else option.text(opt[key]);
						el.append(option);
					});
					labl.text(el.children("option").eq(0).text());
				}
				el.attr('id', $(this).data('filterid'));
				el.addClass('tableFilter form-control');
				if ($(this).data('class')) el.addClass($(this).data('class')).attr('autocomplete', 'nope');
				divIG.append(el).append(labl).append($("<i></i>").addClass("bar"));
				divFC.append(divIG);
				$(this).html(divFC[0].outerHTML);
			} else $(this).html('');
		});
	}
	let filters = $('.tableFilter'),
		drpicker_el = $(".drpicker");
	if (drpicker_el.length) {
		drpicker_el.daterangepicker({
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
		}).on('apply.daterangepicker', function (ev, picker) {
			if (picker.chosenLabel === 'Always') $(this).val('');
			else $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
			$(this).attr('title', $(this).val());
			if ($(this).closest('#data_table').length) datatableAjaxReload();
		}).on('cancel.daterangepicker', function () {
			$(this).attr('title', '');
			$(this).val('');
			if ($(this).closest('#data_table').length) datatableAjaxReload();
		});
	}
	$('[title]').tooltip();
	if (save_button_el.length > 0) {
		saveButton = save_button_el;
	} else {
		saveButton = $("#customSave");
	}
	$.getJSON(folder + "js/en.json", function (data) {
		jsstrings = data;
		if (jslang !== 'en') {
			$.getJSON(folder + "js/" + jslang + ".json", function (data) {
				$.extend(jsstrings, data);
				initComplete();
			});
		} else {
			initComplete();
		}
	});

	function initComplete() {
		if (typeof aoColumns !== 'undefined') {
			dataTable = $('#data_table');
			let dataTableOptions = {
				"bPaginate": true,
				"bLengthChange": true,
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
					});
				},
				"aoColumns": aoColumns,
				"responsive": true,
				"orderCellsTop": true,
				"fixedHeader": true
				// "responsive": {
				// 	"details": {
				// 		"display": $.fn.dataTable.Responsive.display.childRowImmediate,
				// 		"type": ''
				// 	}
				// }
			};
			if (typeof jslang != 'undefined' && jslang !== 'en') {
				dataTableOptions.oLanguage = jsstrings.datatables;
			}
			if (typeof dtSort != 'undefined') {
				dataTableOptions.order = dtSort;
				dataTableOptions.bSort = true;
			}
			if (typeof aoColumnDefs != 'undefined') {
				dataTableOptions.aoColumnDefs = aoColumnDefs;
			}
			table = dataTable.DataTable(dataTableOptions);
			table.on('page.dt', function () {
				let info = table.page.info();
				currentPage = info.page;
			});
			table.on('draw.dt', function () {
				datatableLoadCallback();
			});
			if (typeof responsiveResize != 'undefined') table.on('responsive-resize', function () {
				responsiveResize();
			});
			$.fn.dataTable.ext.errMode = 'none';

			if (typeof delAction !== 'undefined') {
				delAction = jsstrings[delAction];
				_confirmDelete.addClass('modal fade').attr('id', 'confirm_delete').attr('role', 'dialog').attr('aria-hidden', 'true').on('show.bs.modal', function (e) {
					$(this).find('.btn-ok').data('actid', $(e.relatedTarget));
				});
				_confirmDelete.append($('<div></div>').addClass('modal-dialog').append(
					$('<div></div>').addClass('modal-content').append(
						$('<div></div>').addClass('modal-header').append($('<h4></h4>').addClass('modal-title').attr('id', 'myModalLabel').text(jsstrings.confirm_delete)).append($('<button></button>').addClass('close').attr('data-dismiss', 'modal').attr('aria-hidden', 'true').html('&times;'))
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
				body.append(_confirmDelete);
			}
			_messagePopup.addClass('modal').attr('role', 'dialog').attr('aria-hidden', 'true')
				.append($('<div></div>').addClass('modal-dialog').append(
					$('<div></div>').addClass('modal-content').append(
						$('<div></div>').addClass('modal-header').append($('<h4></h4>').addClass('modal-title')).append($('<button></button>').addClass('close').attr('data-dismiss', 'modal').attr('aria-hidden', 'true').html('&times;'))
					)
						.append(
							$('<div></div>').addClass('modal-body').append($('<p></p>'))
						)
						.append(
							$('<div></div>').addClass('modal-footer').append($('<button></button>').addClass('btn btn-ok').attr('data-dismiss', 'modal').text(jsstrings.ok)
							)
						)));
			body.append(_messagePopup);
		}
		if (typeof initCompleteCallback !== 'undefined') initCompleteCallback();
	}

	$("body").on('click', '#add', function () {
		if (typeof CKEDITOR != 'undefined' && typeof CKEDITOR.instances.edcontent != 'undefined') CKEDITOR.instances.edcontent.setData(typeof startData != 'undefined' ? startData : '');
		if (typeof CKEDITOR != 'undefined' && typeof CKEDITOR.instances.eddescription != 'undefined') CKEDITOR.instances.eddescription.setData('');
		$("#edtable").find(":input").each(function () {
			let name = $(this).attr("name");
			if (name && name.substring(0, 2) === "ed") {
				if (name !== 'edimage') {
					if ($(this).children('option[selected]').length) $(this).val($(this).children('option[selected]').val());
					else {
						if ($(this).attr('value')) $(this).val($(this).attr('value'));
						else if ($(this).data('DateTimePicker')) {
							let d = new Date();
							$(this).val(formatDateTime(d));
						} else $(this).val('');
					}
				}
				if ($(this).data('autoComplete')) {
					$(this).autoComplete('set', null);
				}
			}
		});
		saveButton.data("actid", 0).closest('.modal').modal('show').find('.modal-title').eq(0).text(jsstrings.buttonAdd);
		imagePreview.attr('src', folder + 'img/' + imagePreview.data('folder') + '/' + imagePreview.data('default'));
		$('#edimage').data('imgname', '');
	}).on('click', '.btn-export', function () {
		let date = loadData(),
			url = adminfolder + 'json/' + jsonPage,
			inputs = '';
		date.secho = 1;
		date.export = $(this).data('type');
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
			} else inputs += '<input type="hidden" name="' + key + '" value="' + value + '" />';
		});
		$('<form action="' + url + '" method="post">' + inputs + '</form>').appendTo('body').submit().remove();
	}).on('click', 'span.actions', function () {
		let actid = $(this).data('actid') ? $(this).data('actid') : $(this).closest('tr').children('td').eq(0).text();
		if ($(this).hasClass('fa-edit') && !$(this).hasClass("disabled")) {
			popupEdit(actid, $(this).data('cid'), $(this).data('col'));
		}
	});
	$(window).on('keyup', function (e) {
		if (e.which === 27 && $('.modal:visible').length > 0) {
			/*removePic.each(function() {
			 let filename = $(this).data('src');
			 $.post(adminfolder + 'act/Media', {clearImg: filename});
			 });
			 saveButton.data("actid", 0);
			 $('.modal, .modal-backdrop').modal('hide');*/
			e.preventDefault();
		}
	});
	$("#edtable").find(":input").change(function () {
		$(this).removeClass('is-invalid').parent().find('.invalid-feedback').eq(0).remove();
	});
	save_button_el.click(function () {
		if (typeof beforeEdit !== 'undefined') beforeEdit();
		let data = {},
			mdl = $(this).closest('.modal'),
			url = adminfolder + 'act/' + jsonPage,
			method = 'POST',
			_sm = $("#settingsModal");

		if ($(this).data("actid")) {
			url += '/' + $(this).data("actid");
			method = 'PATCH';
		}
		if (typeof jsonForms !== 'undefined') {
			data = jsonForms.data;
			if (jsonForms.errors.length) {
				showToastMessage('error', jsstrings.checkForm);
				data = false;
			}
		} else {
			$("#edtable").find(":input:not(:button)").each(function () {
				let fieldName = $(this).attr("name");
				if (fieldName && fieldName.substring(0, 2) === "ed") {
					$(this).removeClass('is-invalid').parent().find('.invalid-feedback').eq(0).remove();
					fieldName = fieldName.substring(2);
					if (fieldName === 'content') {
						if (!$(this).data('nockedit')) data.content = CKEDITOR.instances.edcontent.getData();
						else {
							data.content = $(this).val();
						}
					} else if (fieldName === 'image') data[fieldName] = $(this).data('imgname');
					else {
						let isArray = false;
						if (fieldName.substr(-2) === '[]') {
							isArray = true;
							fieldName = fieldName.replace("[]", "");
							if (typeof data[fieldName] === 'undefined') data[fieldName] = [];
						}
						if ($(this).data('autoComplete') && $(this).data('autoComplete')._selectedItem.value) {
							if (!isArray) data[fieldName] = $(this).data('autoComplete')._selectedItem.value;
							else data[fieldName].push($(this).data('autoComplete')._selectedItem.value);
						} else {
							if ($(this).data('value')) {
								if ($(this).is(':checkbox')) {
									if ($(this).prop('checked')) {
										if (!isArray) data[fieldName] = $(this).data('value');
										else data[fieldName].push($(this).data('value'));
									}
								} else {
									if (!isArray) data[fieldName] = $(this).data('value');
									else data[fieldName].push($(this).data('value'));
								}
							} else {
								if ($(this).is(':checkbox')) data[fieldName] = ($(this).is(':checked')) ? 1 : 0;
								else {
									if (!isArray) data[fieldName] = $(this).val();
									else {
										if ($(this).data('id')) data[fieldName].push({
											'id': $(this).data('id'),
											'value': $(this).val()
										});
										else data[fieldName].push($(this).val());
									}
								}
							}
						}
					}
				}
			});
		}
		if (_sm.length) {
			_sm.find(":input").each(function () {
				let fieldName = $(this).attr("name");
				if (fieldName && fieldName.substring(0, 2) === "ed") fieldName = fieldName.substring(2);
				if (fieldName) {
					if (fieldName === 'description' && typeof CKEDITOR != 'undefined' && typeof CKEDITOR.instances.eddescription != 'undefined') {
						data.description = CKEDITOR.instances.eddescription.getData();
					} else {
						if (!data.hasOwnProperty(fieldName)) {
							if ($(':input[name="' + $(this).attr("name") + '"]').length > 1) {
								data[fieldName] = [];
								$(':input[type="text"][name="' + $(this).attr("name") + '"]').each(function () {
									if ($(this).data('autoComplete') && $(this).data('autoComplete').hasOwnProperty('_selectedItem') && $(this).data('autoComplete')._selectedItem.hasOwnProperty('value')) data[fieldName].push($(this).data('autoComplete')._selectedItem.value);
									else data[fieldName].push($(this).data('value') || $(this).val());
								});
								$(':input:checked[name="' + $(this).attr("name") + '"]').each(function () {
									data[fieldName].push($(this).data('value') || $(this).val());
								});
							} else {
								if ($(this).data('autoComplete') && $(this).data('autoComplete').hasOwnProperty('_selectedItem') && $(this).data('autoComplete')._selectedItem.hasOwnProperty('value')) data[fieldName] = $(this).data('autoComplete')._selectedItem.value;
								else data[fieldName] = $(this).val();
							}
						}
					}
				}
			});
		}
		if (removePic.length > 0) {
			data.pictures = [];
			removePic.each(function () {
				data.pictures.push($(this).data('src'));
			});
		}
		if (removeFile.length > 0) {
			data.files = [];
			removeFile.each(function () {
				data.files.push({
					'src': $(this).data('src'),
					'title': $(this).closest('li').find('input[type="text"]').eq(0).val()
				});
			});
		}
		if (data) {
			$.ajax({
				url: url,
				type: method,
				data: data,
				dataType: 'text',
				success: function () {
					datatableAjaxReload();
					mdl.modal('hide');
					if (typeof afterEdit !== 'undefined') afterEdit();
				},
				error: function (xhr) {
					try {
						let json = JSON.parse(xhr.responseText);
						if (typeof json !== 'undefined') {
							if (json.hasOwnProperty('error')) {
								if (typeof json.error !== 'string') {
									$.each(json.error, function (index, err) {
										let _input = $("#ed" + index);
										if (_input.length) {
											let _invalidFeedback = $('<div></div>').addClass('invalid-feedback');
											_input.addClass('is-invalid');
											_invalidFeedback.text(jsstrings.invalidField);
											if (jsstrings.hasOwnProperty(err)) _invalidFeedback.html("<b>" + jsstrings[err] + "</b>");
											_input.parent().append(_invalidFeedback);
										}
									});
								} else showToastMessage('error', json.error);
							} else if (json.message) showToastMessage('error', json.message);
						} else {
							showToastMessage('error', json);
						}
					} catch (e) {
						showToastMessage('error', xhr.responseText)
					}
				}
			});
		}
	});
	imagePreview.click(function () {
		$("#edimage").trigger('click');
	});
	$("#edimage").on("change", function () {
		let formData = new FormData(),
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
				} else alert(data.error);
			}
		});
	});
	filters.each(function () {
		let _filter = $(this),
			tr = _filter.parent();
		tr.contents().filter(function () {
			if (this.nodeType === 3 && !_filter.attr('placeholder')) _filter.attr('placeholder', this.textContent).attr('title', this.textContent);
		});
		if (_filter.closest('th').data('autocomplete')) {
			_filter.on('autocomplete.select', function () {
				setTimeout(function () {
					datatableAjaxReload();
				}, 300);
			});
		} else _filter.change(function () {
			datatableAjaxReload();
		});
	});
	$(".filter-datatable").click(function () {
		$("#data_table").toggleClass('searchActive');
		if (typeof table.responsive !== 'undefined') table.responsive.recalc();
	});
	$('#ppEdit').modal({
		backdrop: 'static',
		keyboard: false,
		show: false
	});
	body.on('collapsed.pushMenu', function () {
		$("#mainMenu").children('.sidebar-menu').find('a').tooltip({
			title: function () {
				return $(this).data('title')
			}
		});
	}).on('expanded.pushMenu', function () {
		$("#mainMenu").children('.sidebar-menu').find('a').tooltip('dispose');
	});
	if (galleryModal.length) {
		galleryModal.on('hidden.bs.modal', function () {
			$("body").addClass("modal-open");
		});
	}
});

function datatableAjaxReload() {
	if (typeof table.api !== 'undefined') table.api().ajax.reload();
	else if (typeof table.ajax !== 'undefined') table.ajax.reload();
}

function datatableLoadCallback() {
	$('[title]').tooltip();
	$('[aria-sort]').click(function (e) {
		if ($(e.target).hasClass('tableFilter')) {
			e.stopPropagation();
			e.preventDefault();
		}
	});
}

function proceedDelete(e) {
	if (typeof jsonPage != 'undefined') {
		let deleteId = (e.data('actid')) ? e.data('actid') : e.closest('tr').children('td').eq(0).text();
		$.ajax({
			url: adminfolder + "act/" + jsonPage + "/" + deleteId,
			type: 'DELETE',
			success: function () {
				datatableAjaxReload();
				$('.modal, .modal-backdrop').modal('hide');
			}
		});
	}
}

function popupEdit(actid, cid, col, extra) {
	$("#edtable").find("input").each(function () {
		if ($(this).attr('type') !== 'checkbox' && $(this).attr('type') !== 'radio') {
			if ($(this).attr('value')) $(this).val($(this).attr('value'));
			else $(this).val('');
		} else $(this).prop('checked', false);
	});
	let dataPost = {};
	if (typeof col !== 'undefined' && col) {
		dataPost.filters = {};
		dataPost.filters[col] = cid;
		actid = cid;
	} else dataPost.id = actid;
	if (typeof extra !== 'undefined' && extra) {
		$.each(extra, function (key, value) {
			dataPost[key] = value;
		});
	}
	$("#loading-screen").show();
	$.ajax({
		url: adminfolder + 'json/' + jsonPage,
		type: 'POST',
		data: dataPost,
		dataType: 'json',
		success: function (data) {
			$.each(data, function (key, value) {
				let elm = $("#ed" + key),
					elm_name = $('[name="ed' + key + '[]"]');
				if ((key !== 'content' && key !== 'description') || elm.data('nockedit')) {
					if (!elm.length && elm_name.length) elm = elm_name;
					if (elm.length > 1) {
						$.each(value, function (index, arrvalue) {
							let selectValue = $('[name="ed' + key + '[]"][value="' + arrvalue + '"]');
							if (selectValue.is(':checkbox')) selectValue.prop('checked', true);
							else if ($('[name="ed' + key + '[]"][data-value="' + arrvalue + '"]').is(':checkbox')) $('[name="ed' + key + '[]"][data-value="' + arrvalue + '"]').prop('checked', true);
						});
					} else {
						if (elm.is(':checkbox')) {
							if (value === 1) elm.prop('checked', 'checked');
						} else if ($('[name="ed' + key + '"]').is(':radio')) {
							$('[name="ed' + key + '"][value="' + value + '"]').prop('checked', 'checked').trigger('change');
						} else if (!elm.is(':file') && !elm.data('autoComplete')) elm.val(value);
						if (key === 'image') {
							if (value) imagePreview.attr('src', folder + 'img/' + imagePreview.data('folder') + '/' + value);
							$('#edimage').data('imgname', '');
						}
						if (elm.data('autoComplete')) {
							if (typeof data[key] !== 'object') {
								if (data[key]) elm.autoComplete('set', {
									value: data[key] || '',
									text: data['schema'].hasOwnProperty(key) ? data[data['schema'][key]['table_reference']]['name'] : ''
								});
								else elm.autoComplete('set', null);
							}
						} else if (elm.hasClass('datetimepicker')) {
							elm.data("DateTimePicker").date(value);
						}
					}
				} else {
					CKEDITOR.instances['ed' + key].setData(value);
					if (CKEDITOR.instances['ed' + key].hasOwnProperty('window') && CKEDITOR.instances['ed' + key].window.hasOwnProperty('$')) {
						if (data.hasOwnProperty('css')) {
							let pagecss = data.css.split(",");
							$.each(pagecss, function (kcss, vcss) {
								if (vcss) {
									let csshref = '/css/' + vcss,
										link = document.createElement('link'),
										cssExists = false;
									$.each(CKEDITOR.instances['ed' + key].window.$.document.getElementsByTagName("link"), function () {
										if ($(this).attr('href') === csshref) cssExists = true;
									});
									if (!cssExists) {
										link.rel = 'stylesheet';
										link.type = 'text/css';
										link.href = csshref;
										link.onload = function () {
											$("#loading-screen").hide();
										};
										$(this).remove();
										CKEDITOR.instances['ed' + key].window.$.document.head.appendChild(link);
									}
								}
							});
						}
					}
				}
			});
			if (typeof CKEDITOR != 'undefined' && typeof CKEDITOR.instances.edcontent !== 'undefined') {
				let pagecss = false;
				if (CKEDITOR.instances.edcontent.hasOwnProperty('window') && CKEDITOR.instances.edcontent.window.hasOwnProperty('$')) {
					$.each(CKEDITOR.instances.edcontent.window.$.document.getElementsByTagName("link"), function () {
						if ($(this).attr('href').indexOf('/css') >= 0) pagecss = true;
					});
				}
				if (!pagecss) $("#loading-screen").hide();
			} else $("#loading-screen").hide();
			if (typeof dataEdit === 'function') {
				dataEdit(data);
			}
			eventEdit.data = data;
			window.dispatchEvent(eventEdit);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			console.log(thrownError);
		}
	});
	saveButton.data("actid", actid).closest('.modal').modal('show').find('.modal-title').eq(0).text(jsstrings.buttonEdit);
}

function displayMessage(type, message) {
	let mh = _messagePopup.find('.modal-header').eq(0),
		mtxt = _messagePopup.find('.modal-body').eq(0).find('p').eq(0);
	mh.removeClass('alert-success alert-danger alert-warning alert-info').addClass('alert-' + type).find('h4').eq(0).text(type.replace(/(\b\w)/gi, function (m) {
		return m.toUpperCase();
	}));
	mtxt.html(message);
	_messagePopup.modal('show');
}

function showToastMessage(type, message) {
	let toast = $('<div></div>').addClass('toast').addClass('alert-' + type),
		jtitle = type + "AlertTitle",
		buttonClose = $('<button></button>').attr('type', 'button').addClass('ml-2 mb-1 close').attr('data-dismiss', 'toast').attr('aria-label', 'Close').append($('<span></span>').attr('aria-hidden', 'true').text('×')),
		progressBar = $('<div></div>').addClass('progress-bar').attr('role', 'progressbar').attr('aria-valuenow', 100).attr('aria-valuemin', 0).attr('aria-valuemax', 100),
		toastHeader = $('<div></div>').addClass('toast-header').append($('<strong></strong>').addClass('mr-auto').html(jsstrings[jtitle])).append(buttonClose),
		toastFooter = $('<div></div>').addClass('toast-footer').append($('<div></div>').addClass('progress').append(progressBar));
	progressBar.one('webkitAnimationEnd oanimationend msAnimationEnd animationend',
		function () {
			toast.removeClass('show').one('webkitAnimationEnd oanimationend msAnimationEnd animationend',
				function () {
					$(this).remove();
				});
		});
	buttonClose.click(function () {
		toast.removeClass('show').one('webkitAnimationEnd oanimationend msAnimationEnd animationend',
			function () {
				$(this).remove();
			});
	});
	toast.attr('role', 'alert').attr('aria-live', 'assertive').attr('aria-atomic', 'true').attr('data-delay', '10000');
	toast.append(toastHeader).append($('<div></div>').addClass('toast-body').text(message)).append(toastFooter);
	toast.appendTo($('.toast-wrapper')).addClass('show');
}

function formatDateTime(date) {
	let month = date.getMonth() + 1,
		day = date.getDate(),
		hours = date.getHours(),
		minutes = date.getMinutes(),
		seconds = date.getSeconds();
	month = month < 10 ? '0' + month : month;
	day = day < 10 ? '0' + day : day;
	hours = hours < 10 ? '0' + hours : hours;
	minutes = minutes < 10 ? '0' + minutes : minutes;
	seconds = seconds < 10 ? '0' + seconds : seconds;
	return date.getFullYear() + "/" + month + "/" + day + " " + hours + ':' + minutes + ':' + seconds;
}