var jsonPage = 'News',
    aoColumns = [
        {"mData": "id"},
        {
            "mData": function (e) {
                return e.languages.name;
            }
        },
        {"mData": "title"},
        {
            "mData": function (e) {
                return e.admins.name;
            }
        },
        {"mData": "date_published"},
        {
            "mData": function (e) {
                return $('#statusf').find('option[value="' + e.status + '"]').text();
            }
        },
        {
            "mData": function (e) {
                return "<span class=\"actions btn btn-outline-primary fal fa-edit\"></span><span class=\"actions btn btn-outline-danger fal fa-trash\" title=\"" + jsstrings.delete + "\" data-actid=\"" + e.id + "\" data-toggle=\"modal\" data-target=\"#confirm_delete\"></span>";
            }
        }
    ],
    delAction = 'delete_news';
$(function () {
    $.widget.bridge('uitooltip', $.ui.tooltip);
    CKEDITOR.replace('edcontent', {
        allowedContent: true,
        extraPlugins: 'justify',
        contentsCss: '/css/main.css',
        height: 500
    });
    $(".autocomplete-author").autoComplete({
        resolver: 'custom',
        events: {
            search: function (qry, callback, origJQElement) {
                var data = {};
                data.filters = {};
                data.filters.name = qry;
                $.ajax({
                    type: 'POST',
                    url: 'json/Administrators',
                    data: data,
                    dataType: "json",
                }).done(function (res) {
                    callback(res);
                });
            }
        },
        noResultsText: 'Niciun autor găsit',
        minLength: 3
    });
});
function loadData(aoData) {
    var filters = {};
    $(aoData).each(function (i, val) {
        if (val.name == 'sEcho') filters.secho = val.value;
        else if (val.name == 'iDisplayStart') filters.start = val.value;
        else if (val.name == 'iDisplayLength') filters.length = val.value;
    });
    filters.filters = {};
    if ($("#idf").val() != '') filters.filters['id'] = $("#idf").val();
    if ($("#languagef").val() != '0') filters.filters['language'] = $("#languagef").val();
    if ($("#titlef").val() != '') filters.filters['title'] = $("#titlef").val();
    if ($("#authorf").data('autoComplete')._selectedItem) filters.filters['admin'] = $("#authorf").data('autoComplete')._selectedItem.value;
    if ($("#statusf").val() != '-1') filters.filters['status'] = $("#statusf").val();
    return filters;
}