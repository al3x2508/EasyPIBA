var jsonPage = 'Settings',
    aoColumns = [
        {"mData": "setting"},
        {"mData": "value"},
        {
            "mData": function (e) {
                return "<span class=\"actions btn fa fa-edit\" data-actid=\"" + e.setting + "\"></span> <a href=\"#confirm_delete\" data-bs-toggle=\"modal\" data-bs-target=\"#confirm_delete\" class=\"actions btn fa fa-trash\" data-actid=\"" + e.setting + "\" data-colname=\"setting\"></a> ";
            }
        }
    ],
    delAction = 'delete_setting';

function loadData(aoData) {
    var filters = {};
    $(aoData).each(function (i, val) {
        if (val.name == 'sEcho') filters.secho = val.value;
        else if (val.name == 'iDisplayStart') filters.start = val.value;
        else if (val.name == 'iDisplayLength') filters.length = val.value;
    });
    filters.filters = {};
    if ($("#settingf").val() != '') filters.filters.setting = $("#settingf").val();
    return filters;
}