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
    if ($('[data-filtertype]').length) {
        $("#data_table thead tr").clone(true).appendTo('#data_table thead');
        $('#data_table thead tr:eq(1) th').each(function (i) {
            var el = false,
                filterType = $(this).data('filtertype');
            if (filterType) {
                var divFC = $("<div></div>").addClass("form-group"),
                    divIG = $("<div></div>").addClass("input-group"),
                    labl = $("<label></label>").addClass("control-label");
                switch (filterType) {
                    case 'text':
                        el = $('<input />');
                        el.attr('type', 'text');
                        labl.text($(this).text());
                        if ($(this).data('autocomplete')) el.addClass('ui-autocomplete-input autocomplete-' + $(this).data('autocomplete')).attr('autocomplete', 'nope');
                        if ($(this).data('value')) el.attr('value', $(this).data('value'));
                        if ($(this).data('disabled')) el.attr('disabled', 'true');

                        break;
                    case 'select':
                    case 'select2':
                        el = $('<select></select>');
                        var opt = $(this).data('options');
                        Object.keys(opt).sort(function (a, b) {
                            return a - b;
                        }).forEach(function (key) {
                            var option = $("<option></option>");
                            option.val(key);
                            if (typeof opt[key] === 'object') {
                                option.text(opt[key][0]);
                                if (opt[key][1]) {
                                    option.attr('selected', 'selected');
                                }
                            } else {
                                option.text(opt[key]);
                            }
                            el.append(option);
                        });
                        labl.text($(this).text());

                        break;
                    default:

                        break;
                }
                el.attr('id', $(this).data('filterid'));
                el.addClass('tableFilter form-control');
                if ($(this).data('class')) el.addClass($(this).data('class'));
                el.attr('autocomplete', 'none');
                divIG.append(el).append(labl).append($("<i></i>").addClass("bar"));
                divFC.append(divIG);
                $(this).empty().append(divFC);

                if (filterType === 'select2') {
                    el.select2();
                }
            } else $(this).html('');
        });
    }
    var filters = $('.tableFilter');
    $('[title]').tooltip();
    if ($("#save").length > 0) saveButton = $("#save");
    else saveButton = $("#customSave");
    $.getJSON(folder + "js/en.json", function (data) {
        jsstrings = data;
        if (typeof jslang != 'undefined') {
            $.getJSON(folder + "js/" + jslang + ".json", function (data) {
                $.extend(jsstrings, data);
                initComplete();
            });
        } else initComplete();
    });

    function initComplete() {
        if (typeof aoColumns != 'undefined') {
            dataTable = $('#data_table');
            var dataTableOptions = {
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
                    })
                },
                "aoColumns": aoColumns,
                "orderCellsTop": true,
                "fixedHeader": true,
                "responsive": {
                    "details": {
                        "display": $.fn.dataTable.Responsive.display.childRowImmediate,
                        "type": ''
                    }
                }
            };
            if (typeof jslang != 'undefined') {
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
                var info = table.page.info();
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
                        $('<div></div>').addClass('modal-header').append($('<h4></h4>').addClass('modal-title').attr('id', 'myModalLabel').text(jsstrings.confirm_delete)).append($('<button></button>').addClass('close').attr('data-bs-dismiss', 'modal').attr('aria-hidden', 'true').html('&times;'))
                    )
                        .append(
                            $('<div></div>').addClass('modal-body').append($('<p></p>').text(delAction)).append($('<p></p>').text(jsstrings.confirm_proceed))
                        )
                        .append(
                            $('<div></div>').addClass('modal-footer').append($('<button></button>').addClass('btn btn-default').attr('data-bs-dismiss', 'modal').text(jsstrings.cancel)).append($('<button></button>').addClass('btn btn-danger btn-ok').text(jsstrings.delete).click(function () {
                                    proceedDelete($(this).data('actid'));
                                })
                            )
                        )));
                $('body').append(_confirmDelete);
            }
            _messagePopup.addClass('modal').attr('role', 'dialog').attr('aria-hidden', 'true')
                .append($('<div></div>').addClass('modal-dialog').append(
                    $('<div></div>').addClass('modal-content').append(
                        $('<div></div>').addClass('modal-header').append($('<h4></h4>').addClass('modal-title')).append($('<button></button>').addClass('close').attr('data-bs-dismiss', 'modal').attr('aria-hidden', 'true').html('&times;'))
                    )
                        .append(
                            $('<div></div>').addClass('modal-body').append($('<p></p>'))
                        )
                        .append(
                            $('<div></div>').addClass('modal-footer').append($('<button></button>').addClass('btn btn-ok').attr('data-bs-dismiss', 'modal').text(jsstrings.ok)
                            )
                        )));
            $('body').append(_messagePopup);
        }
        if (typeof initCompleteCallback !== 'undefined') initCompleteCallback();
    }

    if ($(".drpicker").length) {
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
        }).on('apply.daterangepicker', function (ev, picker) {
            if (picker.chosenLabel == 'Always') $(this).val('');
            else $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
            $(this).attr('title', $(this).val());
            if ($(this).closest('#data_table').length) datatableAjaxReload();
        }).on('cancel.daterangepicker', function () {
            $(this).attr('title', '');
            $(this).val('');
            if ($(this).closest('#data_table').length) datatableAjaxReload();
        });
    }
    $("body").on('click', '#add', function () {
        if (typeof CKEDITOR != 'undefined') editor.setData('');
        $("#edtable").find(":input").each(function () {
            var name = $(this).attr("name");
            if (name && name.substring(0, 2) == "ed") {
                if (name != 'edimage') {
                    if ($(this).children('option[selected]').length) $(this).val($(this).children('option[selected]').val());
                    else {
                        if ($(this).attr('value')) $(this).val($(this).attr('value'));
                        else if ($(this).data('DateTimePicker')) {
                            var d = new Date();
                            $(this).val(formatDateTime(d));
                        } else $(this).val('');
                    }
                }
                if ($(this).data('ui-autocomplete')) {
                    if (!$(this).data('ui-autocomplete').hasOwnProperty('selectedItem') || !$(this).data('ui-autocomplete').selectedItem) $(this).data('ui-autocomplete').selectedItem = {id: 0};
                    else $(this).data('ui-autocomplete').selectedItem.id = 0;
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
        var actid = $(this).data('actid') ? $(this).data('actid') : $(this).closest('tr').children('td').eq(0).text();
        if ($(this).hasClass('fa-edit') && !$(this).hasClass("disabled")) {
            $("#edtable").find("input").each(function () {
                if ($(this).attr('type') != 'checkbox') {
                    if ($(this).attr('value')) $(this).val($(this).attr('value'));
                    else $(this).val('');
                } else $(this).prop('checked', false);
            });
            var dataPost = {};
            if ($(this).data('col')) {
                dataPost.filters = {};
                dataPost.filters[$(this).data('col')] = $(this).data('cid');
                actid = $(this).data('cid');
            } else dataPost.id = actid;
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
                        } else {
                            if (key != 'content') {
                                var elm = $("#ed" + key);
                                if (elm.is(':checkbox')) {
                                    if (value == 1) elm.prop('checked', 'checked');
                                } else if (!elm.is(':file')) elm.val(value);
                                else {
                                    if (value) $('#imagePreview').attr('src', folder + 'img/' + $('#imagePreview').data('folder') + '/' + value);
                                    $('#edimage').data('imgname', '');
                                }
                                if (elm.hasClass('ui-autocomplete-input')) {
                                    if (!elm.data('ui-autocomplete').hasOwnProperty('selectedItem') || !elm.data('ui-autocomplete').selectedItem) elm.data('ui-autocomplete').selectedItem = {};
                                    if (!elm.data('ui-autocomplete').selectedItem) elm.data('ui-autocomplete').selectedItem.id = 0;
                                    elm.combobox().data('ui-autocomplete').selectedItem.id = data[key];
                                    if (data[data['schema'][key]['table_reference']]) elm.val(data[data['schema'][key]['table_reference']]['name']);
                                } else if (elm.hasClass('datetimepicker')) {
                                    elm.data("DateTimePicker").date(value);
                                }
                            } else editor.setData(value);
                        }
                    });
                    if (editData && typeof dataEdit == 'function') dataEdit(data);
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
            /*$(".remove-pic").each(function() {
                var filename = $(this).data('src');
                $.post(adminfolder + 'act/Media', {clearImg: filename});
            });
            saveButton.data("actid", 0);
            $('.modal, .modal-backdrop').modal('hide');*/
            e.preventDefault();
        }
    });
    $('#ppEdit [data-bs-dismiss="modal"]').click(function (event) {
        var mdldlg = $(this).closest('.modal'),
            sc = $("#customsave");
        if (!sc.length || sc.data("actid") == 0) {
            var eimg = mdldlg.find("#edimage");
            if (eimg.length > 0 && eimg.data('imgname') != '') $.post(adminfolder + 'act/Media', {clearImg: eimg.data('imgname')});
            $(".remove-pic").each(function () {
                var filename = $(this).data('src');
                $.post(adminfolder + 'act/Media', {clearImg: filename});
            });
        }
    });
    $("#edtable").find(":input").change(function () {
        $(this).removeClass('is-invalid').parent().find('.invalid-feedback').eq(0).remove();
    });
    $("#save").click(function () {
        var data = {},
            mdl = $(this).closest('.modal'),
            url = adminfolder + 'act/' + jsonPage,
            method = 'POST';
        if ($(this).data("actid")) {
            url += '/' + $(this).data("actid");
            method = 'PATCH';
        }
        $("#edtable").find(":input").each(function () {
            var fieldName = $(this).attr("name");
            if (fieldName && fieldName.substring(0, 2) == "ed") {
                $(this).removeClass('is-invalid').parent().find('.invalid-feedback').eq(0).remove();
                fieldName = fieldName.substring(2);
                if (fieldName == 'content') data.content = CKEDITOR.instances.edcontent.getData();
                else if (fieldName == 'image') data[fieldName] = $(this).data('imgname');
                else {
                    var isArray = false;
                    if (fieldName.substr(-2) == '[]') {
                        isArray = true;
                        fieldName = fieldName.replace("[]", "");
                        if (typeof data[fieldName] === 'undefined') data[fieldName] = [];
                    }
                    if ($(this).data('ui-autocomplete') && $(this).data('ui-autocomplete').hasOwnProperty('selectedItem') && $(this).data('ui-autocomplete').selectedItem) {
                        if (!isArray) data[fieldName] = $(this).data('ui-autocomplete').selectedItem.id;
                        else data[fieldName].push($(this).data('ui-autocomplete').selectedItem.id);
                    } else {
                        if ($(this).data('value')) {
                            if (!isArray) data[fieldName] = $(this).data('value');
                            else data[fieldName].push($(this).data('value'));
                        } else {
                            if ($(this).is(':checkbox')) data[fieldName] = ($(this).is(':checked')) ? 1 : 0;
                            else {
                                if (!isArray) data[fieldName] = $(this).val();
                                else data[fieldName].push($(this).val());
                            }
                        }
                    }
                }
            }
        });
        if ($(".remove-pic").length > 0) {
            data.pictures = [];
            $(".remove-pic").each(function () {
                data.pictures.push($(this).data('src'));
            });
        }
        $.ajax({
            url: url,
            type: method,
            data: data,
            dataType: 'text',
            success: function () {
                datatableAjaxReload();
                mdl.modal('hide');
            },
            error: function (xhr) {
                try {
                    var json = JSON.parse(xhr.responseText);
                    if (typeof json !== 'undefined') {
                        if (json.hasOwnProperty('error')) {
                            if (typeof json.error !== 'string') {
                                $.each(json.error, function (index, err) {
                                    var _input = $("#ed" + index);
                                    if (_input.length) {
                                        var _invalidFeedback = $('<div></div>').addClass('invalid-feedback');
                                        _input.addClass('is-invalid');
                                        _invalidFeedback.text(jsstrings.invalidField);
                                        if (jsstrings.hasOwnProperty(err)) _invalidFeedback.html("<b>" + jsstrings[err] + "</b>");
                                        _input.parent().append(_invalidFeedback);
                                    }
                                });
                            } else showToastMessage('danger', json.error);
                        } else if (json.message) showToastMessage('danger', json.message);
                    } else {
                        showToastMessage('danger', json);
                    }
                } catch (e) {
                    showToastMessage('danger', xhr.responseText)
                }
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
                } else alert(data.error);
            }
        });
    });
    filters.each(function () {
        var _filter = $(this),
            tr = _filter.parent();
        tr.contents().filter(function () {
            if (this.nodeType == 3 && !_filter.attr('placeholder')) _filter.attr('placeholder', this.textContent).attr('title', this.textContent);
        });
    });
    filters.change(function () {
        datatableAjaxReload();
    });
    $(".filter-datatable").click(function () {
        $(".dataTable").toggleClass('searchActive');
        if (typeof table.responsive !== 'undefined') table.responsive.recalc();
    });
    $('#ppEdit').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });
    $('body').on('collapsed.pushMenu', function () {
        $("#mainMenu").children('.sidebar-menu').find('a').tooltip({
            title: function () {
                return $(this).data('title')
            }
        });
    }).on('expanded.pushMenu', function () {
        $("#mainMenu").children('.sidebar-menu').find('a').tooltip('dispose');
    });
});
$(window).on('load', function () {
    $(window).resize(function () {
        setBodyHeight();
    });
    setBodyHeight();
});

function setBodyHeight() {
    $('.content-wrapper').css('min-height', $('aside.main-sidebar').outerHeight());
}

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
        var deleteId = (e.data('actid')) ? e.data('actid') : e.closest('tr').children('td').eq(0).text();
        $.ajax({
            url: adminfolder + "act/" + jsonPage + "/" + deleteId,
            type: 'DELETE',
            success: function (result) {
                datatableAjaxReload();
                $('.modal, .modal-backdrop').modal('hide');
            }
        });
    }
}

function displayMessage(type, message) {
    var mh = _messagePopup.find('.modal-header').eq(0),
        mtxt = _messagePopup.find('.modal-body').eq(0).find('p').eq(0);
    mh.removeClass('alert-success alert-danger alert-warning alert-info').addClass('alert-' + type).find('h4').eq(0).text(type.replace(/(\b\w)/gi, function (m) {
        return m.toUpperCase();
    }));
    mtxt.html(message);
    _messagePopup.modal('show');
}

function showToastMessage(type, message) {
    var toast = $('<div></div>').addClass('toast').addClass('alert-' + type),
        jtitle = type + "AlertTitle",
        buttonClose = $('<button></button>').attr('type', 'button').addClass('ml-2 mb-1 btn-close').attr('data-bs-dismiss', 'toast').attr('aria-label', 'Close'),
        progressBar = $('<div></div>').addClass('progress-bar').attr('role', 'progressbar').attr('aria-valuenow', 100).attr('aria-valuemin', 0).attr('aria-valuemax', 100),
        toastHeader = $('<div></div>').addClass('toast-header').append($('<strong></strong>').addClass('me-auto').html(jsstrings[jtitle])).append(buttonClose),
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
    toast.attr('role', 'alert').attr('aria-live', 'assertive').attr('aria-atomic', 'true').attr('data-bs-delay', '10000');
    toast.append(toastHeader).append($('<div></div>').addClass('toast-body').text(message)).append(toastFooter);
    toast.appendTo($('.toast-container')).addClass('show');
}

function formatDateTime(date) {
    var month = date.getMonth() + 1,
        day = date.getDate(),
        hours = date.getHours(),
        minutes = date.getMinutes(),
        seconds = date.getSeconds();
    month = month < 10 ? '0' + month : month;
    day = day < 10 ? '0' + day : day;
    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    var strTime = date.getFullYear() + "/" + month + "/" + day + " " + hours + ':' + minutes + ':' + seconds;
    return strTime;
}