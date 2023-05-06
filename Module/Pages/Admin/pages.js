var jsonPage = 'Pages',
    aoColumns = [
        {"mData": "id"},
        {"mData": "url"},
        {
            "mData": function (e) {
                return e.languages.name;
            }
        },
        {"mData": "title"},
        {"mData": "menu_text"},
        {
            "mData": function (e) {
                return "<span class=\"actions btn fa fa-folder-open\" data-url=\"/" + e.url + "\"></span><span class=\"actions btn fa fa-edit\"></span><span class=\"actions btn fa fa-trash\"></span>";
            }
        }
    ];

function loadData(aoData) {
    var filters = {};
    $(aoData).each(function (i, val) {
        if (val.name == 'sEcho') filters.secho = val.value;
        else if (val.name == 'iDisplayStart') filters.start = val.value;
        else if (val.name == 'iDisplayLength') filters.length = val.value;
    });
    filters.filters = {};
    if ($("#idf").val() != '') filters.filters['id'] = $("#idf").val();
    if ($("#urlf").val() != '') filters.filters['url'] = $("#urlf").val();
    if ($("#languagef").val() != '0') filters.filters['language'] = $("#languagef").val();
    if ($("#titlef").val() != '') filters.filters['title'] = $("#titlef").val();
    if ($("#menutextf").val() != '') filters.filters['menu_text'] = $("#menutextf").val();
    return filters;
}

$(document).ready(function () {
    ClassicEditor.create(document.querySelector('#edcontent'), {
        allowedContent: true,
    }).then(newEditor => {
        editor = newEditor;
    });
    $("body").on('click', 'span.actions', function () {
        if ($(this).hasClass('fa-folder-open')) window.open($(this).data('url'), '_blank');
    });
    $('#edlanguage').select2({
        dropdownParent: $("#edlanguage").parent(),
        width: '100%'
    });
    $("#edkeywords").tagsinput('items');
});