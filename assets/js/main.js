var jsstrings = {};
$(document).ready(function() {
    var folder = $("#mainjs").data('appdir');
    $(".language").click(function() {
        document.cookie = "language=" + $(this).data('language') + "; path=" + folder;
    });
    $.getJSON(folder + "js/en.json", function(data) {
        jsstrings = data;
        var jslang = document.documentElement.lang;
        if (jslang != 'en') {
            $.getJSON(folder + "js/" + jslang + ".json", function (data) {
                $.extend(jsstrings, data);
            });
        }
    });
});
$(window).on('load', function() {
    $(window).resize();
});
