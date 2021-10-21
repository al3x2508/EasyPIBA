var jsonPage = 'Media',
    aoColumns = [
        {
            "mData": "id",
            "sClass": "text-right"
        },
        {"mData": "filename"},
        {
            "mData": function (e) {
                if (e.type == 1) return "<img src='" + folder + (e.thumbfolder || "uploads/") + e.thumbnail + "' data-src='" + folder + "uploads/" + e.filename + "' />";
                else if (e.type == 2) return "<video controls><source src='" + folder + "uploads/" + e.filename + "'></video>";
                else if (e.type == 3) return "<audio controls><source src='" + folder + "uploads/" + e.filename + "'></audio>";
                else return "<a href='" + folder + (e.thumbfolder || "uploads/") + e.filename + "' target='_blank'><i class='fa fa-file'></i></a>";
            }
        },
        {
            "mData": function (e) {
                return $('#typef').find('option[value="' + e.type + '"]').text();
            }
        },
        {
            "mData": function (e) {
                return !e.thumbfolder ? "<span class=\"actions btn btn-sm btn-outline-danger fa fa-trash\" data-actid=\"" + e.id + "\" data-toggle=\"modal\" data-target=\"#confirm_delete\"></span>" : '';
            }
        }
    ],
    delAction = 'delete_media';

function loadData(aoData) {
    var filters = {};
    $(aoData).each(function (i, val) {
        if (val.name == 'sEcho') filters.secho = val.value;
        else if (val.name == 'iDisplayStart') filters.start = val.value;
        else if (val.name == 'iDisplayLength') filters.length = val.value;
    });
    filters.filters = {};
    if ($("#idf").val() != '') filters.filters['id'] = $("#idf").val();
    if ($("#filenamef").val() != '') filters.filters['filename'] = $("#filenamef").val();
    if ($("#typef").val() != '0') filters.filters['type'] = $("#typef").val();
    return filters;
}

function afterEdit() {
    showToastMessage('success', jsstrings.saved);
}

$(function () {
    $("#data_table").on('click', '[data-src]', function () {
        $("#imgPreview").attr('src', $(this).data('src'));
        $("#preview").modal('show');
    });
});